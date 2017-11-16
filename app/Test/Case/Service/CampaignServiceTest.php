<?php
App::uses('GoalousTestCase', 'Test');
use Goalous\Model\Enum as Enum;

/**
 * Class CampaignServiceTest
 *
 * @property CampaignService  $CampaignService
 * @property ViewCampaignPricePlan $ViewCampaignPricePlan
 * @property PricePlanPurchaseTeam $PricePlanPurchaseTeam
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
        'app.credit_card',
        'app.payment_setting',
        'app.mst_price_plan_group',
        'app.mst_price_plan',
        'app.view_price_plan',
        'app.price_plan_purchase_team',
        'app.payment_setting',
        'app.credit_card'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->setDefaultTeamIdAndUid();
        $this->CampaignService = ClassRegistry::init('CampaignService');
        $this->PaymentService = ClassRegistry::init('PaymentService');
        $this->ViewCampaignPricePlan = ClassRegistry::init('ViewCampaignPricePlan');
        $this->PricePlanPurchaseTeam = ClassRegistry::init('PricePlanPurchaseTeam');
        $this->Team = $this->Team ?? ClassRegistry::init('Team');
    }

    function test_isCampaignTeam()
    {
        $this->createCampaignTeam($teamId = 1, $pricePlanGroupId = 1);
        $this->assertTrue($this->CampaignService->isCampaignTeam(1));
    }

    function test_isCampaignTeam_false()
    {
        $this->assertFalse($this->CampaignService->isCampaignTeam(1));
    }

    function test_purchased()
    {
        $this->createCampaignTeam($teamId = 1, $pricePlanGroupId = 1);
        $this->createPurchasedTeam($teamId = 1, $pricePlanCode = '1-1');
        $this->assertTrue($this->CampaignService->purchased(1));
        $this->createCampaignTeam($teamId = 2, $pricePlanGroupId = 2);
        $this->createPurchasedTeam($teamId = 2, $pricePlanCode = '2-2');
        $this->assertTrue($this->CampaignService->purchased(2));
    }

    function test_purchased_false()
    {
        $this->createCampaignTeam($teamId = 1, $pricePlanGroupId = 1);
        $this->assertFalse($this->CampaignService->purchased(1));
    }

    function test_getTeamPricePlan()
    {
        $this->createCampaignTeam($teamId = 1, $pricePlanGroupId = 1);
        $this->createPurchasedTeam($teamId = 1, $pricePlanCode = '1-1');
        $plan = $this->CampaignService->getTeamPricePlan(1);
        print_r($plan);
        $this->assertEquals([
            'id'          => '1',
            'group_id'    => '1',
            'code'        => '1-1',
            'price'       => '50000',
            'max_members' => '50',
            'currency'    => '1',
        ], $plan);

        $this->createCampaignTeam($teamId = 2, $pricePlanGroupId = 2);
        $this->createPurchasedTeam($teamId = 2, $pricePlanCode = '2-4');
        $plan = $this->CampaignService->getTeamPricePlan(2);
        $this->assertEquals([
            'id'          => '9',
            'group_id'    => '2',
            'code'        => '2-4',
            'price'       => '2000',
            'max_members' => '400',
            'currency'    => '2',
        ], $plan);
    }

    function test_getPricePlanPurchaseTeam()
    {
        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam($pricePlanGroupId = 1, $pricePlanCode = '1-1');
        $res = $this->CampaignService->getPricePlanPurchaseTeam($teamId);
        $this->assertNotEmpty($res);
        $expected = [
            'PricePlanPurchaseTeam' => [
                'id'              => $pricePlanPurchaseId,
                'price_plan_code' => $pricePlanCode
            ],
            'CampaignTeam'          => [
                'id'                  => $campaignTeamId,
                'price_plan_group_id' => $pricePlanGroupId
            ]
        ];
        $this->assertEquals($expected, $res);

        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam($pricePlanGroupId = 2, $pricePlanCode = '2-2');
        $res = $this->CampaignService->getPricePlanPurchaseTeam($teamId);
        $this->assertNotEmpty($res);
        $expected = [
            'PricePlanPurchaseTeam' => [
                'id'              => $pricePlanPurchaseId,
                'price_plan_code' => $pricePlanCode
            ],
            'CampaignTeam'          => [
                'id'                  => $campaignTeamId,
                'price_plan_group_id' => $pricePlanGroupId
            ]
        ];
        $this->assertEquals($expected, $res);
    }

    function test_getTeamPricePlan_notCampaign()
    {
        $this->assertNull($this->CampaignService->getTeamPricePlan(1));
    }

    function test_getMaxAllowedUsers()
    {
        $this->createCampaignTeam($teamId = 1, $pricePlanGroupId = 1);
        $this->createPurchasedTeam($teamId = 1, $pricePlanCode = '1-1');
        $this->assertEquals(50, $this->CampaignService->getMaxAllowedUsers(1));

        $this->createCampaignTeam($teamId = 2, $pricePlanGroupId = 2);
        $this->createPurchasedTeam($teamId = 2, $pricePlanCode = '2-4');
        $this->assertEquals(400, $this->CampaignService->getMaxAllowedUsers(2));
    }

    function test_getMaxAllowedUsers_notCampaign()
    {
        $this->assertEquals(0, $this->CampaignService->getMaxAllowedUsers(2));
    }

    function test_findList()
    {
        $this->createCampaignTeam($teamId = 1, $pricePlanGroupId = 1);
        $this->assertEquals([
            [
                'id'               => '1',
                'sub_total_charge' => '¥50,000',
                'tax'              => '¥4,000',
                'total_charge'     => '¥54,000',
                'member_count'     => '50',
            ],
            [
                'id'               => '2',
                'sub_total_charge' => '¥100,000',
                'tax'              => '¥8,000',
                'total_charge'     => '¥108,000',
                'member_count'     => '200',
            ],
            [
                'id'               => '3',
                'sub_total_charge' => '¥150,000',
                'tax'              => '¥12,000',
                'total_charge'     => '¥162,000',
                'member_count'     => '300',
            ],
            [
                'id'               => '4',
                'sub_total_charge' => '¥200,000',
                'tax'              => '¥16,000',
                'total_charge'     => '¥216,000',
                'member_count'     => '400',
            ],
            [
                'id'               => '5',
                'sub_total_charge' => '¥250,000',
                'tax'              => '¥20,000',
                'total_charge'     => '¥270,000',
                'member_count'     => '500',
            ],
        ], $this->CampaignService->findList(1));

        $this->createCampaignTeam($teamId = 2, $pricePlanGroupId = 2);
        $this->assertEquals([
            [
                'id'               => '6',
                'sub_total_charge' => '$500',
                'tax'              => '$0',
                'total_charge'     => '$500',
                'member_count'     => '50',
            ],
            [
                'id'               => '7',
                'sub_total_charge' => '$1,000',
                'tax'              => '$0',
                'total_charge'     => '$1,000',
                'member_count'     => '200',
            ],
            [
                'id'               => '8',
                'sub_total_charge' => '$1,500',
                'tax'              => '$0',
                'total_charge'     => '$1,500',
                'member_count'     => '300',
            ],
            [
                'id'               => '9',
                'sub_total_charge' => '$2,000',
                'tax'              => '$0',
                'total_charge'     => '$2,000',
                'member_count'     => '400',
            ],
            [
                'id'               => '10',
                'sub_total_charge' => '$2,500',
                'tax'              => '$0',
                'total_charge'     => '$2,500',
                'member_count'     => '500',
            ],
        ], $this->CampaignService->findList(2));
    }

    function test_getPricePlanCurrency()
    {
        $currency = $this->CampaignService->getPricePlanCurrency('1-2');
        $this->assertEquals(1, $currency);

        $currency = $this->CampaignService->getPricePlanCurrency('2-4');
        $this->assertEquals(2, $currency);
    }

    function test_getPricePlanCurrency_noPricePlan()
    {
        $this->assertNull($this->CampaignService->getPricePlanCurrency('1000-1000'));
    }

    function test_savePricePlanPurchase()
    {
        $this->createCampaignTeam($teamId = 1, $pricePlanGroupId = 1);
        $ret = $this->CampaignService->savePricePlanPurchase($teamId, $pricePlanCode = '1-2');
        $expected = [
            'team_id'         => $teamId,
            'price_plan_code' => $pricePlanCode,
        ];
        $this->assertEquals($expected, array_intersect_key($expected, $ret['PricePlanPurchaseTeam']));

        $this->createCampaignTeam($teamId = 2, $pricePlanGroupId = 2);
        $ret = $this->CampaignService->savePricePlanPurchase($teamId, $pricePlanCode = '2-1');
        $expected = [
            'team_id'         => $teamId,
            'price_plan_code'   => $pricePlanCode,
        ];
        $this->assertEquals($expected, array_intersect_key($expected, $ret['PricePlanPurchaseTeam']));
    }

    public function providerAllowedPricePlanGroupJPY()
    {
        $groupId = 1;
        foreach (range(1, 5) as $detailNo) {
            yield [$groupId.'-'.$detailNo, $companyCountry = 'JP'];
        }
    }

    /**
     * @dataProvider providerAllowedPricePlanGroupJPY
     *
     * @param string $pricePlanCode
     * @param string $companyCountry
     */
    function test_isAllowedPricePlanGroupJPY(string $pricePlanCode, string $companyCountry)
    {
        $this->createCampaignTeam($createTeamId = 1, $pricePlanGroupId = 1);// Plan Group of JPY
        $this->assertTrue($this->CampaignService->isAllowedPricePlan($teamId = 1, $pricePlanCode, $companyCountry));

        $this->createCampaignTeam($createTeamId = 2, $pricePlanGroupId = 2);// Plan Group of USD
        $this->assertFalse($this->CampaignService->isAllowedPricePlan($teamId = 2, $pricePlanCode, $companyCountry));
    }

    public function providerAllowedPricePlanGroupUSD()
    {
        $groupId = 2;
        foreach (range(1, 5) as $detailNo) {
            foreach (['DE', 'TH', 'US'] as $companyCountry) {
                yield [$groupId.'-'.$detailNo, $companyCountry];
            }
        }
    }

    /**
     * @dataProvider providerAllowedPricePlanGroupUSD
     *
     * @param string $pricePlanCode
     * @param string $companyCountry
     */
    function test_isAllowedPricePlanGroupUSD(string $pricePlanCode, string $companyCountry)
    {
        $this->createCampaignTeam($createTeamId = 1, $pricePlanGroupId = 2);// Plan Group of USD
        $this->assertTrue($this->CampaignService->isAllowedPricePlan($teamId = 1, $pricePlanCode, $companyCountry));

        $this->createCampaignTeam($createTeamId = 2, $pricePlanGroupId = 1);// Plan Group of JPY
        $this->assertFalse($this->CampaignService->isAllowedPricePlan($teamId = 2, $pricePlanCode, $companyCountry));
    }

    function test_getChargeInfo()
    {
        $this->assertEquals([
            'id'               => '1',
            'sub_total_charge' => '50000',
            'tax'              => floatval(4000),
            'total_charge'     => floatval(54000),
            'member_count'     => '50',
        ], $this->CampaignService->getChargeInfo('1-1'));
        $this->assertEquals([
            'id'               => '5',
            'sub_total_charge' => '250000',
            'tax'              => floatval(20000),
            'total_charge'     => floatval(270000),
            'member_count'     => '500',
        ], $this->CampaignService->getChargeInfo('1-5'));
        $this->assertEquals([
            'id'               => '6',
            'sub_total_charge' => '500',
            'tax'              => floatval(0),
            'total_charge'     => floatval(500),
            'member_count'     => '50',
        ], $this->CampaignService->getChargeInfo('2-1'));
        $this->assertEquals([
            'id'               => '10',
            'sub_total_charge' => '2500',
            'tax'              => floatval(0),
            'total_charge'     => floatval(2500),
            'member_count'     => '500',
        ], $this->CampaignService->getChargeInfo('2-5'));
    }

    function test_getTeamChargeInfo()
    {
        list ($teamId) = $this->createCcCampaignTeam($pricePlanGroupId = 1, $pricePlanCode = '1-1');
        $this->assertEquals([
            'id'               => '1',
            'sub_total_charge' => '50000',
            'tax'              => floatval(4000),
            'total_charge'     => floatval(54000),
            'member_count'     => '50',
        ], $this->CampaignService->getTeamChargeInfo($teamId));

        list ($teamId) = $this->createCcCampaignTeam($pricePlanGroupId = 1, $pricePlanCode = '1-4');
        $this->assertEquals([
            'id'               => '4',
            'sub_total_charge' => '200000',
            'tax'              => floatval(16000),
            'total_charge'     => floatval(216000),
            'member_count'     => '400',
        ], $this->CampaignService->getTeamChargeInfo($teamId));
    }

    function test_willExceedMaximumCampaignAllowedUser()
    {
        $this->createCampaignTeam($teamId = 1, $pricePlanGroupId = 1);
        $this->createPurchasedTeam($teamId = 1, $pricePlanCode = '1-1');
        for ($x = 0; $x < 50; $x++) {
            $this->createActiveUser(1);
        }
        $exceed = $this->CampaignService->willExceedMaximumCampaignAllowedUser($teamId = 1, $additionalUsersCount = 1);
        $this->assertTrue($exceed, 'team member count 50 exceed if adding 1 user');

        $this->createCampaignTeam($teamId = 2, $pricePlanGroupId = 1);
        $this->createPurchasedTeam($teamId = 2, $pricePlanCode = '1-1');
        $exceed = $this->CampaignService->willExceedMaximumCampaignAllowedUser($teamId = 2, $additionalUsersCount = 1);
        $this->assertFalse($exceed, 'team member count below 50 not exceeding');
    }

    function test_findPlansForUpgrading()
    {
        $pricePlanCode = '1-1';
        $pricePlanGroupId = 1;
        //  Prepare data for testing
        list($teamId, $paymentSettingId) = $this->createCcPaidTeam();
        $campaignTeamId = $this->createCampaignTeam($teamId, $pricePlanGroupId);
        $purchasedPlan = $this->CampaignService->savePricePlanPurchase($teamId, $pricePlanCode);
        $purchasedPlan = Hash::get($purchasedPlan,'PricePlanPurchaseTeam');

        $res = $this->CampaignService->findPlansForUpgrading($teamId, []);
        $this->assertEmpty($res);

        $currentPlan = $this->CampaignService->getPlanByCode($pricePlanCode);
        $res = $this->CampaignService->findPlansForUpgrading($teamId, $currentPlan);
        $this->assertNotEmpty($res);
        $this->assertTrue(count($res) > 1);

        $plans = Hash::extract(
            $this->ViewCampaignPricePlan->find('all', [
                'conditions' => [
                    'group_id' => 1
                ],
                ['order' => 'max_members ASC']
            ]),
            '{n}.ViewCampaignPricePlan'
        );
        print_r($res);
        foreach ($res as $i => $v) {
            $this->assertEquals($v['id'], $plans[$i]['id']);
            $this->assertEquals($v['code'], $plans[$i]['code']);
            $this->assertEquals($v['max_members'], $plans[$i]['max_members']);
            $this->assertEquals($v['price'], $plans[$i]['price']);
            $this->assertEquals($v['format_price'], $this->PaymentService->formatCharge($plans[$i]['price'], $plans[$i]['currency']));

            if ($v['code'] == $pricePlanCode) {
                $this->assertTrue($v['is_current_plan']);
            } else {
                $this->assertFalse($v['is_current_plan']);
            }

            if ($v['max_members'] <= $currentPlan['max_members']) {
                $this->assertArrayNotHasKey('sub_total_charge', $res[$i]);
                $this->assertArrayNotHasKey('tax', $res[$i]);
                $this->assertArrayNotHasKey('total_charge', $res[$i]);
                $this->assertFalse($v['can_select']);
            } else {
                $this->assertTrue($v['can_select']);
            }
        }

        // plan_id: 1以外
        $pricePlanCode = '1-3';
        $this->PricePlanPurchaseTeam->id = $purchasedPlan['id'];
        $this->PricePlanPurchaseTeam->save(['price_plan_code' => $pricePlanCode], false);
        $currentPlan = $this->CampaignService->getPlanByCode($pricePlanCode);
        $res = $this->CampaignService->findPlansForUpgrading($teamId, $currentPlan);
        $this->assertNotEmpty($res);
        $this->assertTrue(count($res) > 1);

        $plans = Hash::extract(
            $this->ViewCampaignPricePlan->find('all', [
                'conditions' => [
                    'group_id' => 1
                ],
                ['order' => 'max_members ASC']
            ]),
            '{n}.ViewCampaignPricePlan'
        );
        foreach ($res as $i => $v) {
            $this->assertEquals($v['id'], $plans[$i]['id']);
            $this->assertEquals($v['code'], $plans[$i]['code']);
            $this->assertEquals($v['max_members'], $plans[$i]['max_members']);
            $this->assertEquals($v['price'], $plans[$i]['price']);
            $this->assertEquals($v['format_price'], $this->PaymentService->formatCharge($plans[$i]['price'], $plans[$i]['currency']));

            if ($v['code'] == $pricePlanCode) {
                $this->assertTrue($v['is_current_plan']);
            } else {
                $this->assertFalse($v['is_current_plan']);
            }

            if ($v['max_members'] <= $currentPlan['max_members']) {
                $this->assertArrayNotHasKey('sub_total_charge', $res[$i]);
                $this->assertArrayNotHasKey('tax', $res[$i]);
                $this->assertArrayNotHasKey('total_charge', $res[$i]);
                $this->assertFalse($v['can_select']);
            } else {
                $this->assertTrue($v['can_select']);
            }
        }

        //US
        // TODO
    }

    function test_upgradePlan()
    {
        $this->markTestSkipped();
    }

    function test_findAllPlansByGroupId_yen()
    {
        $pricePlanGroupId = 1;
        $res = $this->CampaignService->findAllPlansByGroupId($pricePlanGroupId);
        $this->assertEquals(count($res), 5);
        $this->assertEquals(reset($res), [
            'id' => 1,
            'code' => '1-1',
            'price' => 50000,
            'currency' => Enum\PaymentSetting\Currency::JPY,
            'group_id' => $pricePlanGroupId,
            'max_members' => 50,
        ]);
        $this->assertEquals(end($res), [
            'id' => 5,
            'code' => '1-5',
            'price' => 250000,
            'currency' => Enum\PaymentSetting\Currency::JPY,
            'group_id' => $pricePlanGroupId,
            'max_members' => 500,
        ]);

        $redisData = $this->GlRedis->getMstCampaignPlans($pricePlanGroupId);
        $this->assertEquals($res, $redisData);

        $pgCache = $this->CampaignService->getCachePlans();
        $this->assertEquals($res, $pgCache[$pricePlanGroupId]);

        $this->CampaignService->clearCachePlans();
        $res = $this->CampaignService->findAllPlansByGroupId($pricePlanGroupId);
        $this->assertEquals(count($res), 5);

        $this->CampaignService->clearCachePlans();
        $this->GlRedis->deleteMstCampaignPlans($pricePlanGroupId);
        $res = $this->CampaignService->findAllPlansByGroupId($pricePlanGroupId);
        $this->assertEquals(count($res), 5);
    }

    function test_findAllPlansByGroupId_dollar()
    {
        $pricePlanGroupId = 2;
        $res = $this->CampaignService->findAllPlansByGroupId($pricePlanGroupId);
        $this->assertEquals(count($res), 5);
        $this->assertEquals(reset($res), [
            'id' => 6,
            'code' => '2-1',
            'price' => 500,
            'currency' => Enum\PaymentSetting\Currency::USD,
            'group_id' => $pricePlanGroupId,
            'max_members' => 50,
        ]);
        $this->assertEquals(end($res), [
            'id' => 10,
            'code' => '2-5',
            'price' => 2500,
            'currency' => Enum\PaymentSetting\Currency::USD,
            'group_id' => $pricePlanGroupId,
            'max_members' => 500,
        ]);

        $this->ViewCampaignPricePlan->deleteAll(['group_id' => $pricePlanGroupId]);

        $res = $this->CampaignService->findAllPlansByGroupId($pricePlanGroupId);
        $this->assertEquals(count($res), 5);

        $this->GlRedis->deleteMstCampaignPlans($pricePlanGroupId);
        $res = $this->CampaignService->findAllPlansByGroupId($pricePlanGroupId);
        $this->assertEquals(count($res), 5);

        $this->CampaignService->clearCachePlans();
        $res = $this->CampaignService->findAllPlansByGroupId($pricePlanGroupId);
        $this->assertEquals(count($res), 0);
    }

    function tearDown()
    {
        parent::tearDown();

        $this->Team->resetCurrentTeam();
    }
}
