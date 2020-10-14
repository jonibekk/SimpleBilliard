<?php
App::uses('CampaignTeam', 'Model');
App::uses('CreditCard', 'Model');
App::uses('Invoice', 'Model');
App::uses('PricePlanPurchaseTeam', 'Model');
App::uses('User', 'Model');
App::uses('TeamMember', 'Model');
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'TeamService');

/**
 * @property TeamService $TeamService
 */

use Goalous\Enum as Enum;
use Goalous\Enum\Model\Team\ServiceUseStatus as TeamUseStatusEnum;
use Goalous\Enum\Model\PaymentSetting\Type as PaymentTypeEnum;

class TeamServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.term',
        'app.user',
        'app.team',
        'app.team_member',
        'app.payment_setting',
        'app.invoice',
        'app.credit_card',
        'app.price_plan_purchase_team',
        'app.campaign_team',
        'app.circle',
        'app.circle_member',
        'app.email',
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
        $this->TeamService = ClassRegistry::init('TeamService');
        $this->Team = ClassRegistry::init('Team');
    }

    function test_getServiceUseStatus_success()
    {
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->assertEquals($this->TeamService->getServiceUseStatus(), Team::SERVICE_USE_STATUS_FREE_TRIAL);
    }

    function test_getReadOnlyEndDate_success()
    {
        $teamId = $this->createTeam([
            'service_use_status'           => Team::SERVICE_USE_STATUS_READ_ONLY,
            'service_use_state_start_date' => '2017-01-10',
            'service_use_state_end_date'   => '2017-02-09',
        ]);
        $this->setDefaultTeamIdAndUid(1, $teamId);
        $this->TeamService->getStateEndDate();
        $this->assertEquals($this->TeamService->getStateEndDate(), '2017-02-09');
    }

    function test_updateServiceUseStatus_success()
    {
        $teamId = $this->createTeam(['service_use_status' => Team::SERVICE_USE_STATUS_FREE_TRIAL]);
        $this->setDefaultTeamIdAndUid(1, $teamId);

        $res = $this->TeamService->updateServiceUseStatus($teamId, Team::SERVICE_USE_STATUS_PAID, date('Y-m-d'));

        $this->assertTrue($res === true);
        $this->assertEquals($this->TeamService->getServiceUseStatusByTeamId($teamId), Team::SERVICE_USE_STATUS_PAID);

        // Paid to Read-only
        $res = $this->TeamService->updateServiceUseStatus($teamId, Team::SERVICE_USE_STATUS_READ_ONLY, date('Y-m-d'));

        $this->assertTrue($res === true);
        $this->assertEquals($this->TeamService->getServiceUseStatusByTeamId($teamId),
            Team::SERVICE_USE_STATUS_READ_ONLY);
    }

    function test_getTeamTimezone_success()
    {
        $teamId = $this->createTeam([
            'service_use_status'           => Team::SERVICE_USE_STATUS_READ_ONLY,
            'service_use_state_start_date' => '2017-01-10',
            'service_use_state_end_date'   => '2017-02-09',
            'timezone'                     => 9,
        ]);
        $this->setDefaultTeamIdAndUid(1, $teamId);

        // Assert created value
        $timezone = $this->TeamService->getTeamTimezone($teamId);
        $this->assertEquals(9, $timezone);

        // Assert saved value
        $this->Team->save(['id' => $teamId, 'timezone' => 11.0]);
        $timezone = $this->TeamService->getTeamTimezone($teamId);
        $this->assertEquals(11, $timezone);

        // test error
        $timezone = $this->TeamService->getTeamTimezone(987987);
        $this->assertNull($timezone);
    }

    public function test_updateDefaultTeamOnDeletion_success()
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');

        /** @var User $User */
        $User = ClassRegistry::init('User');

        $newData = [
            'team_id'    => 2,
            'user_id'    => 9,
            'last_login' => "2019-05-22 02:28:04",
            'status'     => Enum\Model\TeamMember\Status::ACTIVE
        ];

        $TeamMember->create();
        $TeamMember->save($newData, false);

        $TeamService->updateDefaultTeamOnDeletion(1);

        $user = $User->findById(9);
        $this->assertEquals('2', $user['User']['default_team_id']);

        $user = $User->findById(2);
        $this->assertEquals('2', $user['User']['default_team_id']);

        $user = $User->findById(3);
        $this->assertEquals('2', $user['User']['default_team_id']);

        $user = $User->findById(12);
        $this->assertEquals('1', $user['User']['default_team_id']);

        $user = $User->findById(13);
        $this->assertEquals('1', $user['User']['default_team_id']);
    }

    public function test_changeStatusAllTeamExpired_success()
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');

        $trialTeamId = $this->createTeam([
            'service_use_status'           => TeamUseStatusEnum::FREE_TRIAL,
            'service_use_state_start_date' => '2016-01-01',
            'service_use_state_end_date'   => '2017-01-01',
            'timezone'                     => 9,
        ]);
        $trialNotExpiredTeamId = $this->createTeam([
            'service_use_status'           => TeamUseStatusEnum::FREE_TRIAL,
            'service_use_state_start_date' => '2016-01-01',
            'service_use_state_end_date'   => '2019-01-01',
            'timezone'                     => 9,
        ]);
        $paidTeamId = $this->createPaidTeam('2016-01-01', '2017-01-01');

        $TeamService->changeStatusAllTeamExpired('2018-01-01', TeamUseStatusEnum::FREE_TRIAL,
            TeamUseStatusEnum::READ_ONLY);

        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId)['service_use_status']);
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($trialTeamId)['service_use_status']);
        $this->assertEquals(TeamUseStatusEnum::FREE_TRIAL,
            $Team->getEntity($trialNotExpiredTeamId)['service_use_status']);
    }

    public function test_changeInvoicePaidTeamToReadOnly_success()
    {
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');

        $paidTeamId1 = $this->createPaidTeam('2016-01-01', '2017-01-01', PaymentTypeEnum::INVOICE());
        $paidTeamId2 = $this->createPaidTeam('2016-01-01', '2017-01-02', PaymentTypeEnum::INVOICE());
        $paidTeamId3 = $this->createPaidTeam('2016-01-01', '2017-01-03', PaymentTypeEnum::INVOICE());

        $TeamService->changePaidTeamToReadOnly('2016-12-30');

        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertNotEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertNotEmpty($Invoice->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertNotEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertNotEmpty($Invoice->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertNotEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));
        $this->assertNotEmpty($Invoice->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));

        $TeamService->changePaidTeamToReadOnly('2017-01-02');

        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEmpty($Invoice->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEmpty($Invoice->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertNotEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));
        $this->assertNotEmpty($Invoice->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));

        $TeamService->changePaidTeamToReadOnly('2017-01-03');

        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEmpty($Invoice->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEmpty($Invoice->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));
        $this->assertEmpty($Invoice->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));
    }

    public function test_changeCreditCardPaidTeamToReadOnly_success()
    {
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init('CreditCard');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');
        /** @var PaymentSetting $PaymentSetting */
        $PaymentSetting = ClassRegistry::init('PaymentSetting');

        $paidTeamId1 = $this->createPaidTeam('2016-01-01', '2017-01-01', PaymentTypeEnum::CREDIT_CARD());
        $paidTeamId2 = $this->createPaidTeam('2016-01-01', '2017-01-02', PaymentTypeEnum::CREDIT_CARD());
        $paidTeamId3 = $this->createPaidTeam('2016-01-01', '2017-01-03', PaymentTypeEnum::CREDIT_CARD());

        $TeamService->changePaidTeamToReadOnly('2016-12-30');

        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertNotEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertNotEmpty($CreditCard->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertNotEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertNotEmpty($CreditCard->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertNotEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));
        $this->assertNotEmpty($CreditCard->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));

        $TeamService->changePaidTeamToReadOnly('2017-01-02');

        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEmpty($CreditCard->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEmpty($CreditCard->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertNotEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));
        $this->assertNotEmpty($CreditCard->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));

        $TeamService->changePaidTeamToReadOnly('2017-01-03');

        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEmpty($CreditCard->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEmpty($CreditCard->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertEmpty($PaymentSetting->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));
        $this->assertEmpty($CreditCard->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));
    }

    public function test_changeCampaignPaidTeamToReadOnly_success()
    {
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init('CampaignTeam');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');

        $paidTeamId1 = $this->createPaidTeam('2016-01-01', '2017-01-01', PaymentTypeEnum::INVOICE(), true);
        $paidTeamId2 = $this->createPaidTeam('2016-01-01', '2017-01-02', PaymentTypeEnum::INVOICE(), true);
        $paidTeamId3 = $this->createPaidTeam('2016-01-01', '2017-01-03', PaymentTypeEnum::INVOICE(), true);

        $TeamService->changePaidTeamToReadOnly('2016-12-30');

        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertNotEmpty($CampaignTeam->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertNotEmpty($CampaignTeam->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertNotEmpty($CampaignTeam->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));

        $TeamService->changePaidTeamToReadOnly('2017-01-02');

        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertEmpty($CampaignTeam->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertEmpty($CampaignTeam->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertNotEmpty($CampaignTeam->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));

        $TeamService->changePaidTeamToReadOnly('2017-01-03');

        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertEmpty($CampaignTeam->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertEmpty($CampaignTeam->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertEmpty($CampaignTeam->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));
    }

    public function test_checkPricePlanAfterPaidTeamToReadOnly_success()
    {
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');

        $paidTeamId1 = $this->createPaidTeam('2016-01-01', '2017-01-01', PaymentTypeEnum::INVOICE(), false, true);
        $paidTeamId2 = $this->createPaidTeam('2016-01-01', '2017-01-02', PaymentTypeEnum::INVOICE(), false, true);
        $paidTeamId3 = $this->createPaidTeam('2016-01-01', '2017-01-03', PaymentTypeEnum::INVOICE(), false, true);

        $TeamService->changePaidTeamToReadOnly('2016-12-30');

        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertNotEmpty($PricePlanPurchaseTeam->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertNotEmpty($PricePlanPurchaseTeam->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertNotEmpty($PricePlanPurchaseTeam->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));

        $TeamService->changePaidTeamToReadOnly('2017-01-02');

        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertEmpty($PricePlanPurchaseTeam->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertEmpty($PricePlanPurchaseTeam->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::PAID, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertNotEmpty($PricePlanPurchaseTeam->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));

        $TeamService->changePaidTeamToReadOnly('2017-01-03');

        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId1)['service_use_status']);
        $this->assertEmpty($PricePlanPurchaseTeam->find('first', ['conditions' => ['team_id' => $paidTeamId1]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId2)['service_use_status']);
        $this->assertEmpty($PricePlanPurchaseTeam->find('first', ['conditions' => ['team_id' => $paidTeamId2]]));
        $this->assertEquals(TeamUseStatusEnum::READ_ONLY, $Team->getEntity($paidTeamId3)['service_use_status']);
        $this->assertEmpty($PricePlanPurchaseTeam->find('first', ['conditions' => ['team_id' => $paidTeamId3]]));
    }

    private function createPaidTeam(
        string $startDate,
        string $endDate,
        PaymentTypeEnum $type = null,
        bool $withCampaign = false,
        bool $withPricePlan = false
    ): int
    {

        $team = [
            'service_use_state_start_date' => $startDate,
            'service_use_state_end_date'   => $endDate,
            'timezone'                     => 9
        ];

        if (!empty($type) && $type->getValue() === PaymentTypeEnum::INVOICE) {
            $teamId = $this->createInvoicePaidTeam($team)[0];
        } else {
            $teamId = $this->createCcPaidTeam($team)[0];
        }

        if ($withCampaign) {
            /** @var CampaignTeam $CampaignTeam */
            $CampaignTeam = ClassRegistry::init('CampaignTeam');
            $CampaignTeam->create();
            $CampaignTeam->save([
                'team_id'    => $teamId,
                'start_date' => $startDate,
                'del_flg'    => false
            ], false);
        }

        if ($withPricePlan) {
            /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
            $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');
            $PricePlanPurchaseTeam->create();
            $PricePlanPurchaseTeam->save([
                'team_id'           => $teamId,
                'price_plan_code'   => '1-2',
                'purchase_datetime' => '123',
                'del_flg'           => false
            ], false);
        }

        return $teamId;
    }

    public function test_joinTeam_success()
    {
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');
        $this->assertTrue($TeamService->joinTeam(1, 2));

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $this->assertNotEmpty($TeamMember->getUnique(1, 2));

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        $this->assertCount(1, $CircleMember->getJoinedCircleIds(2, 1));
    }

    public function test_joinTeamAlreadyExist_failed()
    {
        /** @var TeamService $TeamService */
        $TeamService = ClassRegistry::init('TeamService');
        try {
            $TeamService->joinTeam(1, 1);
        } catch (Goalous\Exception\GoalousConflictException $e) {
        } catch (Exception $e) {
            $this->fail();
        }
    }
}
