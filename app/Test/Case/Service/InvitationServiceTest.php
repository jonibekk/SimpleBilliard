<?php
App::uses('GoalousTestCase', 'Test');
App::uses('GoalousDateTime', 'DateTime');
App::import('Service', 'InvitationService');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentFlagClient');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentFlagKey');

use Goalous\Enum as Enum;

/**
 * InvitationServiceTest Class
 *
 * @property InvitationService $InvitationService
 * @property User              $User
 * @property Email             $Email
 * @property Invite            $Invite
 * @property TeamMember        $TeamMember
 */
class InvitationServiceTest extends GoalousTestCase
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
        'app.invite',
        'app.payment_setting',
        'app.charge_history',
        'app.price_plan_purchase_team',
        'app.mst_price_plan_group',
        'app.mst_price_plan',
        'app.view_price_plan',
        'app.campaign_team',
        'app.circle',
        'app.circle_member',
        'app.credit_card',
        'app.experiment'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->InvitationService = ClassRegistry::init('InvitationService');
        $this->User = ClassRegistry::init('User');
        $this->Email = ClassRegistry::init('Email');
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->Invite = ClassRegistry::init('Invite');
        $this->PaymentSetting = ClassRegistry::init('PaymentSetting');
        $paymentKeyFlagClient = new PaymentFlagClient();

        $paymentFlagKey = new PaymentFlagKey(PaymentFlagKey::SWITCH_FLAG_NAME);
        $paymentKeyFlagClient->write($paymentFlagKey, 1);
        $paymentDateKey = new PaymentFlagKey(PaymentFlagKey::SWITCH_START_DATE_NAME);
        $paymentKeyFlagClient->write($paymentDateKey, '20191217');
    }

    /**
     * Validate emails
     * check empty
     */
    function test_validateEmails_checkEmpty()
    {
        $teamId = 1;
        $emails = [];
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEquals($errors[0], __("Input is required"));

        $emails = ['', ''];
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEquals($errors[0], __("Input is required"));
    }

    /**
     * Validate emails
     * format
     */
    function test_validateEmails_format()
    {
        $teamId = 1;
        $emails = ['a'];
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEquals($errors[0], __("Line %d", 1) . "：" . __("Email address is incorrect."));

        $emails = ['a@f'];
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEquals($errors[0], __("Line %d", 1) . "：" . __("Email address is incorrect."));

        $emails = ['test@example.com'];
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEmpty($errors);

        $emails = ['a', 'b'];
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEquals($errors[0], __("Line %d", 1) . "：" . __("Email address is incorrect."));
        $this->assertEquals($errors[1], __("Line %d", 2) . "：" . __("Email address is incorrect."));

        $emails = ['', 'a', '', 'b', '', 'test@example.com'];
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEquals(count($errors), 2);
        $this->assertEquals($errors[0], __("Line %d", 2) . "：" . __("Email address is incorrect."));
        $this->assertEquals($errors[1], __("Line %d", 4) . "：" . __("Email address is incorrect."));
    }

    /**
     * Validate emails
     * Check max invitation count
     */
    function test_validateEmails_maxInvitationCount()
    {
        $teamId = 1;
        $emails = [];
        for ($i = 1; $i <= 100; $i++) {
            $emails[] = sprintf("test%d@example.com", $i);
        }
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEmpty($errors);

        $emails[] = "test101@example.com";
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEquals($errors[0],
            __("%s invitations are the limits in one time.", InvitationService::MAX_INVITATION_CNT));
    }

    /**
     * Validate emails
     * Check duplicates
     */
    function test_validateEmails_duplicate()
    {
        $teamId = 1;
        $duplicateErrMsg = "：" . __("%s is duplicated.", __("Email address"));
        $emails = array_fill(0, 2, 'test@example.com');
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEquals($errors[0],
            __("Line %d", 2) . $duplicateErrMsg);

        $emails = array_fill(0, 3, 'test@example.com');
        $errors = $this->InvitationService->validateEmails($teamId, $emails);
        $this->assertEquals($errors[0],
            __("Line %d", 2) . $duplicateErrMsg);
        $this->assertEquals($errors[1],
            __("Line %d", 3) . $duplicateErrMsg);
    }

    /**
     * Validate emails
     * Check if already invited
     */
    function test_validateEmails_alreadyInvited()
    {
        $teamId = 1;
        $email = 'test1@company.com';
        $this->User->create();
        $this->User->save([], false);
        $userId = $this->User->getLastInsertID();
        $res = $this->InvitationService->validateEmails($teamId, [$email]);
        $this->assertEquals($res, []);

        $this->Email->create();
        $this->Email->save([
            'user_id' => $userId,
            'email'   => $email
        ], false);

        $this->TeamMember->save([
            'user_id' => $userId,
            'team_id' => 2,
        ]);
        $res = $this->InvitationService->validateEmails($teamId, [$email]);
        $this->assertEquals($res, []);

        $this->TeamMember->save([
            'user_id' => $userId,
            'team_id' => $teamId,
        ]);
        $res = $this->InvitationService->validateEmails($teamId, [$email]);
        $this->assertTrue(strpos($res[0],
                __("This email address has already been used. Use another email address.")) >= 0);

    }

    /**
     * Invite
     */
    function test_invite_basic()
    {
        $teamId = $this->createTeam([
            'service_use_status' => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL
        ]);

        $email = 'test1@company.com';
        $res = $this->InvitationService->invite($teamId, 1, [$email]);
        $this->assertFalse($res['error']);
        $res = Hash::get($this->Invite->findByTeamId($teamId), 'Invite');
        $this->assertEquals($res['from_user_id'], 1);
        $this->assertEquals($res['email'], $email);
        $this->assertEquals($res['email_verified'], false);
        $this->assertNotEmpty($res['email_token']);
        $this->assertTrue($res['email_token_expires'] <= (REQUEST_TIMESTAMP + TOKEN_EXPIRE_SEC_INVITE));

        $emailData = Hash::get($this->Email->findByEmail($email), 'Email');
        $this->assertNotEmpty($emailData);
        $user = Hash::get($this->User->find('first', ['order' => 'id DESC']), 'User');
        $this->assertNotEmpty($user);
        $this->assertEqual($emailData['user_id'], $user['id']);

        $teamMember = Hash::get($this->TeamMember->getByUserId($user['id'], $teamId), 'TeamMember');
        $this->assertNotEmpty($res);
        $this->assertEquals($teamMember['status'], Enum\Model\TeamMember\Status::INVITED);

        $teamId = $this->createTeam([
            'service_use_status' => Enum\Model\Team\ServiceUseStatus::FREE_TRIAL
        ]);

        $res = $this->InvitationService->invite($teamId, 1, [$email]);
        $this->assertFalse($res['error']);
        $user2 = Hash::get($this->User->find('first', ['order' => 'id DESC']), 'User');
        $this->assertEquals($user, $user2);
        $emailData2 = Hash::get($this->Email->findByEmail($email), 'Email');
        $this->assertEquals($emailData, $emailData2);

        $teamMember = Hash::get($this->TeamMember->getByUserId($user['id'], $teamId), 'TeamMember');
        $this->assertNotEmpty($res);
        $this->assertEquals($teamMember['status'], Enum\Model\TeamMember\Status::INVITED);

        $emails = [
            'test2@company.com',
            'test3@company.com',
            'test4@company.com',
        ];
        $res = $this->InvitationService->invite($teamId, 1, $emails);
        $this->assertFalse($res['error']);
        $teamMembers = Hash::extract($this->TeamMember->findAllByTeamId($teamId), '{n}.TeamMember');
        $this->assertEquals(count($teamMembers), 4);

        // TODO.Payment: add unit test cases related charge
    }

    /**
     * Test user invitations for campaign teams.
     */
    function test_invite_campaign()
    {
        // Assert single user
        $teamId = $this->createTeam([
            'service_use_status' => Enum\Model\Team\ServiceUseStatus::PAID
        ]);
        /*
         * set payment setting data
         */
        $createData = $this->createTestPaymentData(['team_id' => $teamId, 'payment_base_day' => 31]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($createData, false);

        $userId = $this->createActiveUser($teamId);
        $this->createCampaignTeam($teamId, $campaignType = 0, $pricePlanGroupId = 1);
        $this->createPurchasedTeam($teamId, $pricePlanCode = '1-1');

        $emails = ['test1@company.com'];
        $res = $this->InvitationService->invite($teamId, $userId, $emails);
        $this->assertFalse($res['error'] === true);

        // 1 active user + 49 invitations
        $emails = [];
        for ($n = 2; $n < 50; $n++) {
            array_push($emails,"test$n@company.com");
        }
        $res = $this->InvitationService->invite($teamId, $userId, $emails);
        $this->assertFalse($res['error'] === true);

        // Exceeds campaign user limit
        $emails = [
            'test50@company.com',
            'test51@company.com',
        ];
        $res = $this->InvitationService->invite($teamId, $userId, $emails);
        $this->assertTrue($res['error'] === true);
    }

    function test_validateEmail()
    {
        $extractedEmailValidationErrors = $this->InvitationService->validateEmail('example@example.com');
        $this->assertEquals([], $extractedEmailValidationErrors);

        $extractedEmailValidationErrors = $this->InvitationService->validateEmail('not_email_format');
        $this->assertEquals(1, count($extractedEmailValidationErrors['email']));
    }

    public function test_consumeToken_success()
    {
        $invitationToken = "somecustomtoken";

        $newInviteData = $this->createDataInvite(14, 2, "from@email.com", $invitationToken);
        $this->Invite->save($newInviteData, false);
        $createData = $this->createTestPaymentData(
            [
                'type'             => Enum\Model\PaymentSetting\Type::INVOICE,
                'team_id'          => 2,
                'payment_base_day' => 31
            ]);
        $this->PaymentSetting->create();
        $this->PaymentSetting->save($createData, false);

        /** @var InvitationService $InvitationService */
        $InvitationService = ClassRegistry::init('InvitationService');
        $InvitationService->consumeToken(1, $invitationToken);
    }

    private function createDataInvite(int $userIdFrom, int $teamId, string $email, string $token = 'token'): array
    {
        return [
            'from_user_id'        => $userIdFrom,
            'to_user_id'          => null,
            'team_id'             => $teamId,
            'email'               => $email,
            'message'             => '',
            'email_verified'      => false,
            'email_token'         => $token,
            'email_token_expires' => GoalousDateTime::now()->getTimestamp(),
            'del_flg'             => false,
            'deleted'             => null,
            'created'             => GoalousDateTime::now()->getTimestamp(),
            'modified'            => GoalousDateTime::now()->getTimestamp(),
        ];
    }

    private function createDataEmail(int $userId, string $email): array
    {
        return [
            'user_id'             => $userId,
            'email'               => $email,
            'email_verified'      => false,
            'email_token'         => '12345678',
            'email_token_expires' => GoalousDateTime::now()->getTimestamp(),
            'del_flg'             => false,
            'deleted'             => null,
            'created'             => GoalousDateTime::now()->getTimestamp(),
            'modified'            => GoalousDateTime::now()->getTimestamp(),
        ];
    }

    function test_reInvite_success()
    {
        $userIdFrom = 1;
        $userId = 1;
        $teamId = 1;
        $emailFirst = 'reInviteTest@example.com';
        $emailReInvite = 'reInviteTestAgain@example.com';
        $inviteData = $this->Invite->save($this->createDataInvite($userIdFrom, $teamId, $emailFirst));
        $insertedInviteId = $inviteData['Invite']['id'];
        $emailData = $this->Email->save($this->createDataEmail($userId, $emailFirst));
        $result = $this->InvitationService->reInvite($inviteData['Invite'], $emailData['Email'], $emailReInvite);
        $this->assertTrue($result);
        $inviteData = $this->Invite->findById($insertedInviteId);
        $reInviteData = $this->Invite->findById($insertedInviteId + 1)['Invite'];

        // asserting old invite cant get due to del_flg = 1
        $this->assertEquals([], $inviteData);

        $this->assertEquals(false, $reInviteData['del_flg']);
        $this->assertEquals($emailReInvite, $reInviteData['email']);
        $this->assertNull($reInviteData['deleted']);
    }

    function test_reInvite_success_same_email()
    {
        $userIdFrom = 1;
        $userId = 1;
        $teamId = 1;
        $email = 'reInviteTest@example.com';
        $inviteData = $this->Invite->save($this->createDataInvite($userIdFrom, $teamId, $email));
        $insertedInviteId = $inviteData['Invite']['id'];
        $emailData = $this->Email->save($this->createDataEmail($userId, $email));
        $result = $this->InvitationService->reInvite($inviteData['Invite'], $emailData['Email'], $email);
        $this->assertTrue($result);

        $inviteData = $this->Invite->findById($insertedInviteId);
        $reInviteData = $this->Invite->findById($insertedInviteId + 1)['Invite'];

        // asserting old invite cant get due to del_flg = 1
        $this->assertEquals([], $inviteData);

        $this->assertEquals(false, $reInviteData['del_flg']);
        $this->assertEquals($email, $reInviteData['email']);
        $this->assertNull($reInviteData['deleted']);
    }

    public function test_revokeInvitation_success()
    {
        // regist test data
        $userIdFrom = 1;
        $userId = 1;
        $teamId = 1;
        $email = 'reInviteTest@example.com';
        $inviteData = $this->Invite->save($this->createDataInvite($userIdFrom, $teamId, $email));
        $insertedInviteId = $inviteData['Invite']['id'];
        $emailData = $this->Email->save($this->createDataEmail($userId, $email));


        $res1 = $this->Invite->find('first',[
                'conditions' => [
                    'team_id' => $teamId,
                    'email'   => $email,
                    'del_flg' => false
                ]
            ]
        );
        $this->assertCount(1, $res1);

        // excute target function
        $result = $this->InvitationService->revokeInvitation($teamId, $email);

        $res2 = $this->Invite->find('first',[
                'conditions' => [
                    'team_id' => $teamId,
                    'email'   => $email,
                    'del_flg' => false
                ]
            ]
        );
        $this->assertCount(0, $res2);
    }

    public function test_revokeInvitationOnlyCurrentTeam_success()
    {
        // regist test data
        $userIdFrom = 1;
        $userId = 1;
        $teamId1 = 1;
        $teamId2 = 2;
        $email = 'revokeTest@example.com';
        $this->Invite->create();
        $inviteData1 = $this->Invite->save($this->createDataInvite($userIdFrom, $teamId1, $email));
        $this->Invite->create();
        $inviteData2 = $this->Invite->save($this->createDataInvite($userIdFrom, $teamId2, $email));

        $emailData = $this->Email->save($this->createDataEmail($userId, $email));

        $res1 = $this->Invite->find('all',[
                'conditions' => [
                    'email'   => $email,
                    'del_flg' => false
                ]
            ]
        );
        $this->assertCount(2, $res1);

        // excute target function
        $result = $this->InvitationService->revokeInvitation($teamId1, $email);

        $res2 = $this->Invite->find('all',[
                'conditions' => [
                    'email'   => $email,
                    'del_flg' => false
                ]
            ]
        );
        $this->assertCount(1, $res2);
    }

    public function test_revokeInvitation_failure()
    {
        // regist test data
        $userIdFrom = 1;
        $userId = 1;
        $teamId = 1;
        $email = 'revokeTest@example.com';
        $inviteData = $this->Invite->save($this->createDataInvite($userIdFrom, $teamId, $email));
        $insertedInviteId = $inviteData['Invite']['id'];
        $emailData = $this->Email->save($this->createDataEmail($userId, $email));


        $res1 = $this->Invite->find('first',[
                'conditions' => [
                    'team_id' => $teamId,
                    'email'   => $email,
                    'del_flg' => false
                ]
            ]
        );
        $this->assertCount(1, $res1);

        // excute target function
        $result = $this->InvitationService->revokeInvitation($teamId, $email);

        $res2 = $this->Invite->find('first',[
                'conditions' => [
                    'team_id' => $teamId,
                    'email'   => $email,
                    'del_flg' => false
                ]
            ]
        );
        $this->assertCount(0, $res2);
    }

    private function createTestPaymentData(array $data): array
    {
        $default = [
            'type'                           => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'amount_per_user'                => PaymentService::AMOUNT_PER_USER_JPY,
            'company_name'                   => 'ISAO',
            'company_country'                => 'JP',
            'company_post_code'              => '1110111',
            'company_region'                 => 'Tokyo',
            'company_city'                   => 'Taitou-ku',
            'company_street'                 => '*** ****',
            'contact_person_first_name'      => '太郎',
            'contact_person_first_name_kana' => 'タロウ',
            'contact_person_last_name'       => '東京',
            'contact_person_last_name_kana'  => 'トウキョウ',
            'contact_person_tel'             => '123456789',
            'contact_person_email'           => 'test@example.com',
            'payment_base_day'               => 15,
            'currency'                       => Enum\Model\PaymentSetting\Currency::JPY
        ];
        return am($default, $data);
    }
}
