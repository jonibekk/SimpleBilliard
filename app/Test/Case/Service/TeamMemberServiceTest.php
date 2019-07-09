<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'TeamMemberService');
App::uses('TeamMember', 'Model');;
App::uses('User', 'Model');

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
        'app.mst_translation_language'
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

        $this->insertTranslationLanguage($teamId, Enum\Language::ID());
        $this->insertTranslationLanguage($teamId, Enum\Language::MS());

        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguage($teamId, $userId);
        $this->assertEquals(Enum\Language::ID, array_keys($defaultLanguage)[0]);

        $TeamMember->setDefaultTranslationLanguage($teamId, $userId, Enum\Language::MS);
        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguage($teamId, $userId);
        $this->assertEquals(Enum\Language::MS, array_keys($defaultLanguage)[0]);

        $TeamMember->setDefaultTranslationLanguage($teamId, $userId, Enum\Language::JA);
        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguage($teamId, $userId);
        $this->assertEquals(Enum\Language::ID, array_keys($defaultLanguage)[0]);
    }

    public function test_getDefaultTranslationLanguageCodeFromBrowser_success()
    {
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        $teamId = 1;

        $this->insertTranslationLanguage($teamId, Enum\Language::EN());
        $this->insertTranslationLanguage($teamId, Enum\Language::MS());

        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, 1, [Enum\Language::MS, Enum\Language::EN]);
        $this->assertEquals(Enum\Language::MS, $defaultLanguage);

        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, 2, [Enum\Language::ID, Enum\Language::MS]);
        $this->assertEquals(Enum\Language::MS, $defaultLanguage);

        $defaultLanguage = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, 3, [Enum\Language::JA, Enum\Language::DE]);
        $this->assertEquals(Enum\Language::EN, $defaultLanguage);
    }


    public function test_setDefaultTranslationLanguage_success()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        /** @var TeamMemberService $TeamMemberService */
        $TeamMemberService = ClassRegistry::init('TeamMemberService');

        $teamId = 1;
        $userId = 1;

        $TeamMemberService->setDefaultTranslationLanguage($teamId, $userId, Enum\Language::JA);
        $this->assertEquals(Enum\Language::JA, $TeamMember->getDefaultTranslationLanguage($teamId, $userId));

        $TeamMemberService->setDefaultTranslationLanguage($teamId, $userId, Enum\Language::ID);
        $this->assertEquals(Enum\Language::ID, $TeamMember->getDefaultTranslationLanguage($teamId, $userId));

        $TeamMemberService->setDefaultTranslationLanguage($teamId, $userId, Enum\Language::MS, false);
        $this->assertEquals(Enum\Language::ID, $TeamMember->getDefaultTranslationLanguage($teamId, $userId));
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

        $this->insertTranslationLanguage($teamId, Enum\Language::DE());

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
}
