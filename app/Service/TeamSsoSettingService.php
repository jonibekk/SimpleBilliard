<?php

App::import('Service', 'AppService');
App::uses('TeamSsoSetting', 'Model');

class TeamSsoSettingService extends AppService
{
    /**
     * Get the SSO setting of a team
     *
     * @param int $teamId
     *
     * @return TeamSsoSettingEntity|null
     */
    public function getSetting(int $teamId): ?TeamSsoSettingEntity
    {
        /** @var TeamSsoSetting $TeamSsoSetting */
        $TeamSsoSetting = ClassRegistry::init('TeamSsoSetting');

        $setting = $TeamSsoSetting->getSetting($teamId);

        if (empty($setting)) {
            return $setting;
        }

        $setting['public_cert'] = $this->decrypt($setting['public_cert'], $teamId);

        return $setting;
    }

    /**
     * Create new data or update existing sso setting of a team
     *
     * @param int    $teamId
     * @param string $endpoint
     * @param string $issuer
     * @param string $publicCert
     */
    public function addOrUpdateSetting(int $teamId, string $endpoint, string $issuer, string $publicCert): void
    {
        /** @var TeamSsoSetting $TeamSsoSetting */
        $TeamSsoSetting = ClassRegistry::init('TeamSsoSetting');

        $encryptedPublicCert = $this->encrypt($publicCert, $teamId);

        try {
            $this->TransactionManager->begin();
            $setting = $TeamSsoSetting->getSetting($teamId);
            if (empty($setting)) {
                $TeamSsoSetting->addSetting($teamId, $endpoint, $issuer, $encryptedPublicCert);
            } else {
                $TeamSsoSetting->updateSetting($setting['id'], $endpoint, $issuer, $encryptedPublicCert);
            }
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error(
                'Failed to insert team sso setting',
                [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'team_id' => $teamId
                ]
            );
        }
    }

    /**
     * Encrypt data
     *
     * @param string $rawData
     * @param int    $teamId
     *
     * @return string
     */
    private function encrypt(string $rawData, int $teamId): string
    {
        [$key, $iv] = $this->createKeyAndIV($teamId);

        $binaryEncrypted = openssl_encrypt($rawData, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($binaryEncrypted);
    }

    /**
     * Decrypt data
     *
     * @param string $encryptedData
     * @param int    $teamId
     *
     * @return string
     */
    private function decrypt(string $encryptedData, int $teamId): string
    {
        [$key, $iv] = $this->createKeyAndIV($teamId);

        $binaryEncryptedData = base64_decode($encryptedData);

        return openssl_decrypt($binaryEncryptedData, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * Create encryption key and initialization value for a team
     *
     * @param int $teamId
     *
     * @return array
     */
    private function createKeyAndIV(int $teamId): array
    {
        $key = Configure::read('Security.salt');
        $hash = hash('sha256', 'sso_encryption_initialization_value' . $teamId, true);

        $firstPart = substr($hash, 0, 16);
        $secondPart = substr($hash, 16, 16);

        //Initialization Vector must be exactly 16 bytes
        $ivec = $firstPart ^ $secondPart;

        return [$key, $ivec];
    }
}
