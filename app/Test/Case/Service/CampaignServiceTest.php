<?php
App::uses('GoalousTestCase', 'Test');

/**
 * Class CampaignServiceTest
 *
 * @property CampaignService    $CampaignService
 */
class CampaignServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.user',
        'app.team',
        'app.team_member',
        'app.campaign_team',
        'app.mst_price_plan_group',
        'app.mst_price_plan',
        'app.price_plan_purchase_team'
    );

    private $currentDateTime = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->setDefaultTeamIdAndUid();
        $this->currentDateTime = GoalousDateTime::now()->format('Y-m-d H:i:s');
        $this->CampaignService = ClassRegistry::init('CampaignService');
        $this->Team = $this->Team ?? ClassRegistry::init('Team');
    }

    private function _createCampaignTeam(int $teamId, int $campaignType, int $pricePlanGroupId)
    {
        /** @var CampaignTeam $CampaignTeam */
        $CampaignTeam = ClassRegistry::init('CampaignTeam');

        // Create campaign team
        $campaignTeam = [
            'team_id'             => $teamId,
            'campaign_type'       => $campaignType,
            'price_plan_group_id' => $pricePlanGroupId,
            'start_date'          => $this->currentDateTime,
        ];

        $CampaignTeam->create();
        $CampaignTeam->save($campaignTeam);
    }

    private function _createPurchasedTeam(int $teamId, int $pricePlanId, string $pricePlanCode)
    {
        /** @var PricePlanPurchaseTeam $PricePlanPurchaseTeam */
        $PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');

        $PricePlanPurchaseTeam->create();
        $PricePlanPurchaseTeam->save([
            'team_id'           => $teamId,
            'price_plan_id'     => $pricePlanId,
            'price_plan_code'   => $pricePlanCode,
            'purchase_datetime' => $this->currentDateTime,
        ]);
    }

    function test_isCampaignTeam()
    {
        $this->_createCampaignTeam($teamId = 1, $campaignType = 0, $pricePlanGroupId = 1);
        $this->assertTrue($this->CampaignService->isCampaignTeam(1));
    }

    function test_isCampaignTeam_false()
    {
        $this->assertFalse($this->CampaignService->isCampaignTeam(1));
    }

    function test_purchased()
    {
        $this->_createCampaignTeam($teamId = 1, $campaignType = 0, $pricePlanGroupId = 1);
        $this->_createPurchasedTeam($teamId = 1, $pricePlanId = 1, $pricePlanCode = 'JPY50');
        $this->assertTrue($this->CampaignService->purchased(1));
        $this->_createCampaignTeam($teamId = 2, $campaignType = 0, $pricePlanGroupId = 2);
        $this->_createPurchasedTeam($teamId = 2, $pricePlanId = 7, $pricePlanCode = 'USD200');
        $this->assertTrue($this->CampaignService->purchased(2));
    }

    function test_purchased_false()
    {
        $this->_createCampaignTeam($teamId = 1, $campaignType = 0, $pricePlanGroupId = 1);
        $this->assertFalse($this->CampaignService->purchased(1));
    }

    function test_getTeamPricePlan()
    {
        $this->_createCampaignTeam($teamId = 1, $campaignType = 0, $pricePlanGroupId = 1);
        $this->_createPurchasedTeam($teamId = 1, $pricePlanId = 1, $pricePlanCode = 'JPY50');
        $plan = $this->CampaignService->getTeamPricePlan(1);
        $this->assertEquals([
            'id'          => '1',
            'group_id'    => '1',
            'code'        => 'JPY50',
            'price'       => '50000',
            'max_members' => '50',
            'currency'    => '1',
        ], $plan);

        $this->_createCampaignTeam($teamId = 2, $campaignType = 0, $pricePlanGroupId = 2);
        $this->_createPurchasedTeam($teamId = 2, $pricePlanId = 9, $pricePlanCode = 'USD400');
        $plan = $this->CampaignService->getTeamPricePlan(2);
        $this->assertEquals([
            'id'          => '9',
            'group_id'    => '2',
            'code'        => 'USD400',
            'price'       => '2000',
            'max_members' => '400',
            'currency'    => '2',
        ], $plan);
    }

    function test_getTeamPricePlan_notCampaign()
    {
        $this->assertNull($this->CampaignService->getTeamPricePlan(1));
    }

    function test_getMaxAllowedUsers()
    {
        $this->_createCampaignTeam($teamId = 1, $campaignType = 0, $pricePlanGroupId = 1);
        $this->_createPurchasedTeam($teamId = 1, $pricePlanId = 1, $pricePlanCode = 'JPY50');
        $this->assertEquals(50, $this->CampaignService->getMaxAllowedUsers(1));

        $this->_createCampaignTeam($teamId = 2, $campaignType = 0, $pricePlanGroupId = 2);
        $this->_createPurchasedTeam($teamId = 2, $pricePlanId = 9, $pricePlanCode = 'USD400');
        $this->assertEquals(400, $this->CampaignService->getMaxAllowedUsers(2));
    }

    function test_getMaxAllowedUsers_notCampaign()
    {
        $this->assertEquals(0, $this->CampaignService->getMaxAllowedUsers(2));
    }

    function test_findList()
    {
        $this->_createCampaignTeam($teamId = 1, $campaignType = 0, $pricePlanGroupId = 1);
        $this->assertEquals([
            ['id' => '1', 'sub_total_charge' =>  '¥50,000', 'tax' =>  '¥4,000', 'total_charge' =>  '¥54,000', 'member_count' =>  '50',],
            ['id' => '2', 'sub_total_charge' => '¥100,000', 'tax' =>  '¥8,000', 'total_charge' => '¥108,000', 'member_count' => '200',],
            ['id' => '3', 'sub_total_charge' => '¥150,000', 'tax' => '¥12,000', 'total_charge' => '¥162,000', 'member_count' => '300',],
            ['id' => '4', 'sub_total_charge' => '¥200,000', 'tax' => '¥16,000', 'total_charge' => '¥216,000', 'member_count' => '400',],
            ['id' => '5', 'sub_total_charge' => '¥250,000', 'tax' => '¥20,000', 'total_charge' => '¥270,000', 'member_count' => '500',],
        ], $this->CampaignService->findList(1));

        $this->_createCampaignTeam($teamId = 2, $campaignType = 0, $pricePlanGroupId = 2);
        $this->assertEquals([
            ['id' =>  '6', 'sub_total_charge' =>   '$500', 'tax' => '$0', 'total_charge' =>   '$500', 'member_count' =>  '50',],
            ['id' =>  '7', 'sub_total_charge' => '$1,000', 'tax' => '$0', 'total_charge' => '$1,000', 'member_count' => '200',],
            ['id' =>  '8', 'sub_total_charge' => '$1,500', 'tax' => '$0', 'total_charge' => '$1,500', 'member_count' => '300',],
            ['id' =>  '9', 'sub_total_charge' => '$2,000', 'tax' => '$0', 'total_charge' => '$2,000', 'member_count' => '400',],
            ['id' => '10', 'sub_total_charge' => '$2,500', 'tax' => '$0', 'total_charge' => '$2,500', 'member_count' => '500',],
        ], $this->CampaignService->findList(2));
    }

    public function providerAllowedPricePlanGroupJPY()
    {
        foreach (range(1, 5) as $pricePlanId) {
            yield [$pricePlanId, $companyCountry = 'JP'];
        }
    }

    /**
     * @dataProvider providerAllowedPricePlanGroupJPY
     */
    function test_isAllowedPricePlanGroupJPY(int $pricePlanId, string $companyCountry)
    {
        $this->_createCampaignTeam($createTeamId = 1, $campaignType = 0, $pricePlanGroupId = 1);// Plan Group of JPY
        $this->assertTrue($this->CampaignService->isAllowedPricePlan($teamId = 1, $pricePlanId, $companyCountry));

        $this->_createCampaignTeam($createTeamId = 2, $campaignType = 0, $pricePlanGroupId = 2);// Plan Group of USD
        $this->assertFalse($this->CampaignService->isAllowedPricePlan($teamId = 2, $pricePlanId, $companyCountry));
    }

    public function providerAllowedPricePlanGroupUSD()
    {
        foreach (range(6, 10) as $pricePlanId) {
            foreach (['DE', 'TH', 'US'] as $companyCountry) {
                yield [$pricePlanId, $companyCountry];
            }
        }
    }

    /**
     * @dataProvider providerAllowedPricePlanGroupUSD
     */
    function test_isAllowedPricePlanGroupUSD(int $pricePlanId, string $companyCountry)
    {
        $this->_createCampaignTeam($createTeamId = 1, $campaignType = 0, $pricePlanGroupId = 2);// Plan Group of USD
        $this->assertTrue($this->CampaignService->isAllowedPricePlan($teamId = 1, $pricePlanId, $companyCountry));

        $this->_createCampaignTeam($createTeamId = 2, $campaignType = 0, $pricePlanGroupId = 1);// Plan Group of JPY
        $this->assertFalse($this->CampaignService->isAllowedPricePlan($teamId = 2, $pricePlanId, $companyCountry));
    }

    function test_getChargeInfo()
    {
        $this->assertEquals([
            'id'               => '1',
            'sub_total_charge' => '50000',
            'tax'              => floatval(4000),
            'total_charge'     => floatval(54000),
            'member_count'     => '50',
        ], $this->CampaignService->getChargeInfo(1));
        $this->assertEquals([
            'id'               => '5',
            'sub_total_charge' => '250000',
            'tax'              => floatval(20000),
            'total_charge'     => floatval(270000),
            'member_count'     => '500',
        ], $this->CampaignService->getChargeInfo(5));
        $this->assertEquals([
            'id'               => '6',
            'sub_total_charge' => '500',
            'tax'              => floatval(0),
            'total_charge'     => floatval(500),
            'member_count'     => '50',
        ], $this->CampaignService->getChargeInfo(6));
        $this->assertEquals([
            'id'               => '10',
            'sub_total_charge' => '2500',
            'tax'              => floatval(0),
            'total_charge'     => floatval(2500),
            'member_count'     => '500',
        ], $this->CampaignService->getChargeInfo(10));
    }

    function test_willExceedMaximumCampaignAllowedUser()
    {
        $this->_createCampaignTeam($teamId = 1, $campaignType = 0, $pricePlanGroupId = 1);
        $this->_createPurchasedTeam($teamId = 1, $pricePlanId = 1, $pricePlanCode = 'JPY50');
        for ($x = 0; $x < 50; $x++) {
            $this->createActiveUser(1);
        }
        $exceed = $this->CampaignService->willExceedMaximumCampaignAllowedUser($teamId = 1, $additionalUsersCount = 1);
        $this->assertTrue($exceed, 'team member count 50 exceed if adding 1 user');

        $this->_createCampaignTeam($teamId = 2, $campaignType = 0, $pricePlanGroupId = 1);
        $this->_createPurchasedTeam($teamId = 2, $pricePlanId = 1, $pricePlanCode = 'JPY50');
        $exceed = $this->CampaignService->willExceedMaximumCampaignAllowedUser($teamId = 2, $additionalUsersCount = 1);
        $this->assertFalse($exceed, 'team member count below 50 not exceeding');
    }

    function tearDown()
    {
        parent::tearDown();

        $this->Team->resetCurrentTeam();
    }
}