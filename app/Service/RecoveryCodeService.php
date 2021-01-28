<?php

App::import('Service', 'AppService');
App::uses('RecoveryCode', 'Model');

class RecoveryCodeService extends AppService
{
    // Invalidate TwoFa Auth.
    public function invalidateTwoFa(int $userId): bool
    {
        /** @var RecoveryCode $RecoveryCode */
        $RecoveryCode = ClassRegistry::init('RecoveryCode');

        try {
            $this->TransactionManager->begin();
            $success = $RecoveryCode->invalidateAll($userId);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Failed to invalidate 2FA.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'success' => $success
            ]);
            return false;
        }

        return $success;
    }

    public function generateRecoveryCodes(int $userId): bool
    {
        /** @var RecoveryCode $RecoveryCode */
        $RecoveryCode = ClassRegistry::init('RecoveryCode');

        try {
            $this->TransactionManager->begin();
            $success = $RecoveryCode->regenerate($userId);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Failed to generate recovery codes.', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'success' => $success
            ]);
            return false;
        }

        return $success;
    }

    public function getRecoveryCodes(int $userId): array
    {
        /** @var RecoveryCode $RecoveryCode */
        $RecoveryCode = ClassRegistry::init('RecoveryCode');

        return $RecoveryCode->getAll($userId);
    }
}
