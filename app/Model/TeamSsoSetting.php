<?php

App::uses('AppModel', 'Model');
App::uses('TeamSsoSettingEntity', 'Model/Entity');

class TeamSsoSetting extends AppModel
{
    /**
     * Add sso setting to a team.
     *
     * @param int    $teamId
     * @param string $endpoint   SAML2.0 Endpoint of the IDP
     * @param string $issuer     Issuer of the IdP account
     * @param string $publicCert Encrypted public certificate for the SAML2.0 endpoint
     *
     * @throws Exception
     */
    public function addSetting(int $teamId, string $endpoint, string $issuer, string $publicCert): void
    {
        $newData = [
            'team_id'     => $teamId,
            'endpoint'    => $endpoint,
            'idp_issuer'  => $issuer,
            'public_cert' => $publicCert,
            'created'     => GoalousDateTime::now()->getTimestamp()
        ];

        $this->create();
        $this->save($newData, false);
    }

    /**
     * Update sso setting of a team.
     *
     * @param int    $settingId
     * @param string $endpoint   SAML2.0 Endpoint of the IDP
     * @param string $issuer     Issuer of the IdP account
     * @param string $publicCert Encrypted public certificate for the SAML2.0 endpoint
     *
     * @throws Exception
     */
    public function updateSetting(int $settingId, string $endpoint, string $issuer, string $publicCert): void
    {
        $newData = [
            'endpoint'    => $endpoint,
            'idp_issuer'  => $issuer,
            'public_cert' => $publicCert,
            'modified'    => GoalousDateTime::now()->getTimestamp()
        ];

        $this->clear();
        $this->id = $settingId;

        $this->save($newData, false);
    }

    /**
     * Get sso setting of a team
     *
     * @param int $teamId
     *
     * @return TeamSsoSettingEntity | null
     */
    public function getSetting(int $teamId): ?TeamSsoSettingEntity
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId
            ]
        ];

        /** @var TeamSsoSettingEntity $return */
        $return = $this->useType()->useEntity()->find('first', $option);
        if (empty($return)) {
            return null;
        }

        return $return;
    }
}
