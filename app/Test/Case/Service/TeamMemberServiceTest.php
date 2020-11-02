<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'TeamMemberService');
App::uses('TeamMember', 'Model');;
App::uses('User', 'Model');
App::uses('Email', 'Model');


/**
 * @property TeamMemberService $TeamMemberService
 */

use Goalous\Enum as Enum;

class TeamMemberServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.email',
        'app.user',
        'app.team',
        'app.team_member',
        'app.team_translation_language',
        'app.mst_translation_language',
        'app.cache_unread_circle_post',
        'app.circle_member'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->TeamMemberService = ClassRegistry::init('TeamMemberService');
    }

    public function test_validateActivation()
    {
        // free trial
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $teamMemberId = $this->createTeamMember($teamId, 1, TeamMember::USER_STATUS_INACTIVE);
        $this->assertTrue($this->TeamMemberService->validateActivation($teamId, $teamMemberId));

        // paid
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_PAID]);
        $teamMemberId = $this->createTeamMember($teamId, 2, TeamMember::USER_STATUS_INACTIVE);
        $this->assertTrue($this->TeamMemberService->validateActivation($teamId, $teamMemberId));
    }

    public function test_validateActivation_notAllowedTeamPlan()
    {
        // free trial
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_READ_ONLY]);
        $teamMemberId = $this->createTeamMember($teamId, 1, TeamMember::USER_STATUS_INACTIVE);
        $this->assertFalse($this->TeamMemberService->validateActivation($teamId, $teamMemberId));

        // can not use service
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_CANNOT_USE]);
        $teamMemberId = $this->createTeamMember($teamId, 2, TeamMember::USER_STATUS_INACTIVE);
        $this->assertFalse($this->TeamMemberService->validateActivation($teamId, $teamMemberId));
    }

    public function test_validateActivation_BelongTeam()
    {
        $teamAId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $teamBId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $teamAMemberId = $this->createTeamMember($teamAId, 1, TeamMember::USER_STATUS_INACTIVE);
        $teamBMemberId = $this->createTeamMember($teamBId, 1, TeamMember::USER_STATUS_INACTIVE);
        $this->assertFalse($this->TeamMemberService->validateActivation($teamAId, $teamBMemberId));
    }

    public function test_validateActivation_notAllowdTeamMemberStatus()
    {
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $teamMemberAId = $this->createTeamMember($teamId, 1, TeamMember::USER_STATUS_INVITED);
        $teamMemberBId = $this->createTeamMember($teamId, 1, TeamMember::USER_STATUS_ACTIVE);
        $this->assertFalse($this->TeamMemberService->validateActivation($teamId, $teamMemberAId));
        $this->assertFalse($this->TeamMemberService->validateActivation($teamId, $teamMemberBId));
    }

    public function test_inactivateTeamMember_success()
    {
        $teamMemberId = 1;

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        /** @var User $User */
        $User = ClassRegistry::init('User');

        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        $TeamMemberService->inactivate($teamMemberId);

        $teamMember = $TeamMember->findById($teamMemberId)['TeamMember'];

        $this->assertEquals(Enum\Model\TeamMember\Status::INACTIVE, $teamMember['status']);
        $this->assertEmpty($User->findById($teamMember['user_id'])['User']['default_team_id']);
    }

    public function test_inactivateTeamMemberUpdateDefaultTeamID_success()
    {
        $teamMemberId = 1;
        $userId = 1;
        $oldTeamId = 1;
        $newTeamId = 2;

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        /** @var User $User */
        $User = ClassRegistry::init('User');

        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        //Insert & update required data
        $User->updateDefaultTeam($oldTeamId, true, $userId);

        $newTeamMember = [
            'user_id'    => $userId,
            'team_id'    => $newTeamId,
            'status'     => Enum\Model\TeamMember\Status::ACTIVE,
            'last_login' => 1000
        ];

        $TeamMember->create();
        $TeamMember->save($newTeamMember);

        $TeamMemberService->inactivate($teamMemberId);

        $teamMember = $TeamMember->findById($teamMemberId)['TeamMember'];

        $this->assertEquals(Enum\Model\TeamMember\Status::INACTIVE, $teamMember['status']);
        $this->assertEquals($newTeamId, $User->findById($teamMember['user_id'])['User']['default_team_id']);
    }

    public function test_getDefaultTranslationLanguage_success()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        $teamId = 1;
        $userId = 1;

        $this->insertTranslationLanguage($teamId, "id");
        $this->insertTranslationLanguage($teamId, "ms");

        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguage($teamId, $userId);
        $this->assertEquals("id", array_keys($defaultLanguage)[0]);

        $TeamMember->setDefaultTranslationLanguage($teamId, $userId, "ms");
        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguage($teamId, $userId);
        $this->assertEquals("ms", array_keys($defaultLanguage)[0]);

        $TeamMember->setDefaultTranslationLanguage($teamId, $userId, "ja");
        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguage($teamId, $userId);
        $this->assertEquals("id", array_keys($defaultLanguage)[0]);
    }

    public function test_getDefaultTranslationLanguageCodeFromBrowser_success()
    {
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        $teamId = 1;

        $this->insertTranslationLanguage($teamId, "en");
        $this->insertTranslationLanguage($teamId, "ms");

        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, 1, ["ms", "en"]);
        $this->assertEquals("ms", $defaultLanguage);

        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, 2, ["id", "ms"]);
        $this->assertEquals("ms", $defaultLanguage);

        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, 3, ["ja", "de"]);
        $this->assertEquals("en", $defaultLanguage);
    }

    public function test_setDefaultTranslationLanguage_success()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        $teamId = 1;
        $userId = 1;

        $TeamMemberService->setDefaultTranslationLanguage($teamId, $userId, "ja");
        $this->assertEquals("ja", $TeamMember->getDefaultTranslationLanguage($teamId, $userId));

        $TeamMemberService->setDefaultTranslationLanguage($teamId, $userId, "id");
        $this->assertEquals("id", $TeamMember->getDefaultTranslationLanguage($teamId, $userId));

        $TeamMemberService->setDefaultTranslationLanguage($teamId, $userId, "ms", false);
        $this->assertEquals("id", $TeamMember->getDefaultTranslationLanguage($teamId, $userId));
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_getDefaultTranslationLanguageMemberNotExist_failure()
    {
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        $teamId = 1;
        $userId = 839182;

        $this->insertTranslationLanguage($teamId, "de");

        $TeamMemberService->getDefaultTranslationLanguage($teamId, $userId);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_getDefaultTranslationLanguageTeamNoLanguage_failure()
    {
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        $teamId = 1;
        $userId = 1;

        $TeamMemberService->getDefaultTranslationLanguage($teamId, $userId);
    }

    public function test_updateDelFlgToRevoke_success()
    {
        /** @var Email $Email */
        $Email = ClassRegistry::init('Email');

        // regist test data
        $teamId = 999;
        $userId = 999;
        $email  = 'test999@isao.co.jp';

        $this->TeamMember->create();
        $this->TeamMember->save([
            'user_id'    => $userId,
            'team_id'    => $teamId
        ]);

        $Email->save([
            'email'   => $email,
            'user_id' => $userId,
        ]);

        $res1 = $this->TeamMember->getIdByTeamAndUserId($teamId, $userId);
        $this->assertNotNull($res1);

        // excute target function
        $this->TeamMemberService->updateDelFlgToRevoke($teamId, $email);

        $res2 = $this->TeamMember->getIdByTeamAndUserId($teamId, $userId);
        $this->assertNull($res2);
    }

    public function test_updateDelFlgToRevokeOnlyCurrentTeam_success()
    {

        /** @var Email $Email */
        $Email = ClassRegistry::init('Email');

        // regist test data
        $teamId1 = 999;
        $teamId2 = 1000;
        $userId = 999;
        $email  = 'test999@isao.co.jp';

        $this->TeamMember->create();
        $this->TeamMember->save([
            'user_id'    => $userId,
            'team_id'    => $teamId1
        ]);
        $this->TeamMember->create();
        $this->TeamMember->save([
            'user_id'    => $userId,
            'team_id'    => $teamId2
        ]);

        $Email->save([
            'email'   => $email,
            'user_id' => $userId,
        ]);

        $res1 = $this->TeamMember->find('all', [
                'conditions' => [
                    'user_id' => $userId,
                    'del_flg' => false
                ]
            ]
        );
        $this->assertCount(2, $res1);

        // excute target function
        $this->TeamMemberService->updateDelFlgToRevoke($teamId1, $email);

        $res2 = $this->TeamMember->find('all', [
                'conditions' => [
                    'user_id' => $userId,
                    'del_flg' => false
                ]
            ]
        );
        $this->assertCount(1, $res2);
    }

    public function test_updateDelFlgToRevokeNotFoundUserId_failure()
    {

        /** @var Email $Email */
        $Email = ClassRegistry::init('Email');

        // regist test data
        $teamId = 999;
        $userId = 999;
        $email  = 'test999@isao.co.jp';
        $teamIdFailure = 998;
        $userIdFailure = 998;
        $emailFailure  = 'test998@isao.co.jp';

        $this->TeamMember->create();
        $this->TeamMember->save([
            'user_id'    => $userId,
            'team_id'    => $teamId
        ]);

        $Email->save([
            'email'   => $email,
            'user_id' => $userId,
        ]);

        $res1 = $this->TeamMember->getIdByTeamAndUserId($teamId, $userId);
        $this->assertNotNull($res1);

        // exeute target function
        $res2 = $this->TeamMemberService->updateDelFlgToRevoke($teamIdFailure, $emailFailure);

        $this->assertFalse($res2);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_updateDelFlgToRevokeAlreadyDeleted_failure()
    {

        /** @var Email $Email */
        $Email = ClassRegistry::init('Email');

        // regist test data
        $teamId = 999;
        $userId = 999;
        $email  = 'test999@isao.co.jp';

        $this->TeamMember->create();
        $this->TeamMember->save([
            'user_id'    => $userId,
            'team_id'    => $teamId,
            'del_flg'    => true
        ]);

        $Email->save([
            'email'   => $email,
            'user_id' => $userId,
        ]);

        // excute target function
        $this->TeamMemberService->updateDelFlgToRevoke($teamId, $email);
    }
}
