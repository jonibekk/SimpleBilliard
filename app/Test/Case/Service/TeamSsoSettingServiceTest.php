<?php

App::uses('GoalousTestCase', 'Test');
App::import('Service', 'TeamSsoSettingService');
App::uses('TeamSsoSetting', 'Model');

class TeamSsoSettingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team_sso_setting'
    ];

    public function test_addSetting_success()
    {
        /** @var TeamSsoSettingService $TeamSsoSettingService */
        $TeamSsoSettingService = ClassRegistry::init('TeamSsoSettingService');

        $teamId = 91;

        $TeamSsoSettingService->addOrUpdateSetting(
            $teamId,
            $this->getSampleSsoSetting($teamId)['endpoint'],
            $this->getSampleSsoSetting($teamId)['idp_issuer'],
            $this->getSampleSsoSetting($teamId)['public_cert']
        );
    }

    public function test_getSetting_success()
    {
        /** @var TeamSsoSetting $TeamSsoSetting */
        $TeamSsoSetting = ClassRegistry::init('TeamSsoSetting');
        /** @var TeamSsoSettingService $TeamSsoSettingService */
        $TeamSsoSettingService = ClassRegistry::init('TeamSsoSettingService');

        $teamId = 11;

        $return = $TeamSsoSettingService->getSetting($teamId);

        $this->assertEmpty($return);

        $ssoSetting = $this->getSampleSsoSetting($teamId);

        $TeamSsoSettingService->addOrUpdateSetting(
            $teamId,
            $ssoSetting['endpoint'],
            $ssoSetting['idp_issuer'],
            $ssoSetting['public_cert']
        );

        $return = $TeamSsoSettingService->getSetting($teamId);

        $this->assertTrue($return instanceof TeamSsoSettingEntity);

        $this->assertTextEquals($ssoSetting['endpoint'], $return['endpoint']);
        $this->assertTextEquals($ssoSetting['idp_issuer'], $return['idp_issuer']);
        $this->assertTextEquals($ssoSetting['public_cert'], $return['public_cert']);

        $rawData = $TeamSsoSetting->getSetting($teamId);
        //Check whether data is actually has been altered (encrypted)
        $this->assertTextNotEquals($ssoSetting['public_cert'], $rawData['public_cert']);
    }

    public function test_updateSetting_success()
    {
        /** @var TeamSsoSettingService $TeamSsoSettingService */
        $TeamSsoSettingService = ClassRegistry::init('TeamSsoSettingService');

        $teamId = 13;
        $anotherTeamId = 22;
        $ssoSetting = $this->getSampleSsoSetting($teamId);

        $TeamSsoSettingService->addOrUpdateSetting(
            $teamId,
            $ssoSetting['endpoint'],
            $ssoSetting['idp_issuer'],
            $ssoSetting['public_cert']
        );
        $TeamSsoSettingService->addOrUpdateSetting(
            $anotherTeamId,
            $ssoSetting['endpoint'],
            $ssoSetting['idp_issuer'],
            $ssoSetting['public_cert']
        );

        $newCert = "completelynewcertificate";

        $TeamSsoSettingService->addOrUpdateSetting(
            $teamId,
            $ssoSetting['endpoint'],
            $ssoSetting['idp_issuer'],
            $newCert
        );

        $return = $TeamSsoSettingService->getSetting($teamId);

        $this->assertTrue($return instanceof TeamSsoSettingEntity);

        $this->assertTextEquals($ssoSetting['endpoint'], $return['endpoint']);
        $this->assertTextEquals($ssoSetting['idp_issuer'], $return['idp_issuer']);
        $this->assertTextEquals($newCert, $return['public_cert']);

        $return = $TeamSsoSettingService->getSetting($anotherTeamId);

        $this->assertTrue($return instanceof TeamSsoSettingEntity);

        $this->assertTextEquals($ssoSetting['endpoint'], $return['endpoint']);
        $this->assertTextEquals($ssoSetting['idp_issuer'], $return['idp_issuer']);
        $this->assertTextEquals($ssoSetting['public_cert'], $return['public_cert']);
    }

    private function getSampleSsoSetting(int $teamId): array
    {
        return [
            'endpoint'    => "https://somesampleidp.com/12345" . $teamId,
            'idp_issuer'  => "https://somesampleidp.com/" . $teamId,
            'public_cert' => "anykindofcertificate" . $teamId
        ];
    }
}
