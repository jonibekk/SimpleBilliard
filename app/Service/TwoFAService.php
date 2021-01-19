<?php

App::uses('RecoveryCode', 'Model');
App::uses('User', 'Model');
App::import('Service', 'AppService');
App::import('Vendor', 'paragmarx/google2fa/src/Google2FA');

use Goalous\Exception as GlException;

class TwoFAService extends AppService
{
    /**
     * Generate new secret key for totp 2fa
     *
     * @param int $length
     *
     * @return string
     */
    public function generateSecretKey(int $length = 16): string
    {
        $Google2Fa = new PragmaRX\Google2FA\Google2FA();

        return $Google2Fa->generateSecretKey($length);
    }

    /**
     * Verify 2fa totp token
     *
     * @param int    $userId
     * @param string $twoFaCode
     *
     * @return bool
     */
    public function verifyCode(int $userId, string $twoFaCode): bool
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $user = $User->getById($userId);

        if (empty($user)) {
            throw new GlException\GoalousNotFoundException("User not found");
        }

        if (empty($user['2fa_secret'])) {
            throw new GlException\GoalousException("User does not have 2fa secret");
        }

        $Google2Fa = new PragmaRX\Google2FA\Google2FA();

        return $Google2Fa->verifyKey($user['2fa_secret'], $twoFaCode);
    }

    /**
     * Use a 2fa recovery code
     *
     * @param int    $userId
     * @param string $backupCode
     *
     * @return bool
     * @throws Exception
     */
    public function useBackupCode(int $userId, string $backupCode): bool
    {
        /** @var RecoveryCode $RecoveryCode */
        $RecoveryCode = ClassRegistry::init('RecoveryCode');

        $code = $RecoveryCode->findUnusedCode($userId, $backupCode);

        if (empty($code)) {
            return false;
        }

        try {
            $this->TransactionManager->begin();
            $RecoveryCode->useCode($code['RecoveryCode']['id']);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            throw $e;
        }

        return true;
    }
}