<?php
App::uses('GoalousTestCase', 'Test');

use Goalous\Enum as Enum;

/**
 * Class CampaignServiceTest
 *
 * @property CampaignService       $CampaignService
 * @property ViewCampaignPricePlan $ViewCampaignPricePlan
 * @property PricePlanPurchaseTeam $PricePlanPurchaseTeam
 * @property CampaignTeam          $CampaignTeam
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
        'app.credit_card',
        'app.charge_history',
        'app.invoice',
        'app.invoice_history',
        'app.invoice_histories_charge_history',
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
        $this->CampaignTeam = ClassRegistry::init('CampaignTeam');
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
        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam($pricePlanGroupId = 1,
            $pricePlanCode = '1-1');
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

        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam($pricePlanGroupId = 2,
            $pricePlanCode = '2-2');
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
        $campaignTeamId = $this->createCampaignTeam($teamId = 1, $pricePlanGroupId = 1);
        $expected = [
            [
                'id'               => '1',
                'code'             => '1-1',
                'group_id'         => $pricePlanGroupId,
                'price'            => 50000,
                'currency'         => 1,
                'can_select'       => true,
                'sub_total_charge' => '¥50,000',
                'format_price'     => '¥50,000',
                'tax'              => '¥5,000',
                'total_charge'     => '¥55,000',
                'max_members'      => '50',
            ],
            [
                'id'               => '2',
                'code'             => '1-2',
                'group_id'         => $pricePlanGroupId,
                'price'            => 100000,
                'currency'         => 1,
                'can_select'       => true,
                'sub_total_charge' => '¥100,000',
                'format_price'     => '¥100,000',
                'tax'              => '¥10,000',
                'total_charge'     => '¥110,000',
                'max_members'      => '200',
            ],
            [
                'id'               => '3',
                'code'             => '1-3',
                'group_id'         => $pricePlanGroupId,
                'price'            => 150000,
                'currency'         => 1,
                'can_select'       => true,
                'sub_total_charge' => '¥150,000',
                'format_price'     => '¥150,000',
                'tax'              => '¥15,000',
                'total_charge'     => '¥165,000',
                'max_members'      => '300',
            ],
            [
                'id'               => '4',
                'code'             => '1-4',
                'group_id'         => $pricePlanGroupId,
                'price'            => 200000,
                'currency'         => 1,
                'can_select'       => true,
                'sub_total_charge' => '¥200,000',
                'format_price'     => '¥200,000',
                'tax'              => '¥20,000',
                'total_charge'     => '¥220,000',
                'max_members'      => '400',
            ],
            [
                'id'               => '5',
                'code'             => '1-5',
                'group_id'         => $pricePlanGroupId,
                'price'            => 250000,
                'currency'         => 1,
                'can_select'       => true,
                'sub_total_charge' => '¥250,000',
                'format_price'     => '¥250,000',
                'tax'              => '¥25,000',
                'total_charge'     => '¥275,000',
                'max_members'      => '500',
            ],
        ];
        $res = $this->CampaignService->findList(1);
        $this->assertEquals($expected, $res);

        $pricePlanGroupId = 2;
        $this->CampaignTeam->clear();
        $this->CampaignTeam->id = $campaignTeamId;
        $this->CampaignTeam->save(['price_plan_group_id' => $pricePlanGroupId], false);
        $tax = $this->PaymentService->formatCharge(0, Enum\Model\PaymentSetting\Currency::USD);
        $expected = [
            [
                'id'               => '6',
                'code'             => '2-1',
                'group_id'         => $pricePlanGroupId,
                'price'            => 500,
                'currency'         => 2,
                'can_select'       => true,
                'format_price'     => '$5.00',
                'sub_total_charge' => '$5.00',
                'tax'              => $tax,
                'total_charge'     => '$5.00',
                'max_members'      => '50',
            ],
            [
                'id'               => '7',
                'code'             => '2-2',
                'group_id'         => $pricePlanGroupId,
                'price'            => 1000,
                'currency'         => 2,
                'can_select'       => true,
                'format_price'     => '$10.00',
                'sub_total_charge' => '$10.00',
                'tax'              => $tax,
                'total_charge'     => '$10.00',
                'max_members'      => '200',
            ],
            [
                'id'               => '8',
                'code'             => '2-3',
                'group_id'         => $pricePlanGroupId,
                'price'            => 1500,
                'currency'         => 2,
                'can_select'       => true,
                'format_price'     => '$15.00',
                'sub_total_charge' => '$15.00',
                'tax'              => $tax,
                'total_charge'     => '$15.00',
                'max_members'      => '300',
            ],
            [
                'id'               => '9',
                'code'             => '2-4',
                'group_id'         => $pricePlanGroupId,
                'price'            => 2000,
                'currency'         => 2,
                'can_select'       => true,
                'format_price'     => '$20.00',
                'sub_total_charge' => '$20.00',
                'tax'              => $tax,
                'total_charge'     => '$20.00',
                'max_members'      => '400',
            ],
            [
                'id'               => '10',
                'code'             => '2-5',
                'group_id'         => $pricePlanGroupId,
                'price'            => 2500,
                'currency'         => 2,
                'can_select'       => true,
                'format_price'     => '$25.00',
                'sub_total_charge' => '$25.00',
                'tax'              => $tax,
                'total_charge'     => '$25.00',
                'max_members'      => '500',
            ],
        ];
        $res = $this->CampaignService->findList($teamId);
        $this->assertEquals($expected, $res);

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
            'price_plan_code' => $pricePlanCode,
        ];
        $this->assertEquals($expected, array_intersect_key($expected, $ret['PricePlanPurchaseTeam']));
    }

    public function providerAllowedPricePlanGroupJPY()
    {
        $groupId = 1;
        foreach (range(1, 5) as $detailNo) {
            yield [$groupId . '-' . $detailNo, $companyCountry = 'JP'];
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
                yield [$groupId . '-' . $detailNo, $companyCountry];
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
            'tax'              => floatval(5000),
            'total_charge'     => floatval(55000),
            'member_count'     => '50',
        ], $this->CampaignService->getChargeInfo('1-1'));
        $this->assertEquals([
            'id'               => '5',
            'sub_total_charge' => '250000',
            'tax'              => floatval(25000),
            'total_charge'     => floatval(275000),
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
            'tax'              => floatval(5000),
            'total_charge'     => floatval(55000),
            'member_count'     => '50',
        ], $this->CampaignService->getTeamChargeInfo($teamId));

        list ($teamId) = $this->createCcCampaignTeam($pricePlanGroupId = 1, $pricePlanCode = '1-4');
        $this->assertEquals([
            'id'               => '4',
            'sub_total_charge' => '200000',
            'tax'              => floatval(20000),
            'total_charge'     => floatval(220000),
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
        $purchasedPlan = Hash::get($purchasedPlan, 'PricePlanPurchaseTeam');

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
        foreach ($res as $i => $v) {
            $this->assertEquals($v['id'], $plans[$i]['id']);
            $this->assertEquals($v['code'], $plans[$i]['code']);
            $this->assertEquals($v['max_members'], $plans[$i]['max_members']);
            $this->assertEquals($v['price'], $plans[$i]['price']);
            $this->assertEquals($v['format_price'],
                $this->PaymentService->formatCharge($plans[$i]['price'], $plans[$i]['currency']));

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
            $this->assertEquals($v['format_price'],
                $this->PaymentService->formatCharge($plans[$i]['price'], $plans[$i]['currency']));

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
    }

    function test_upgradePlan_ccJp()
    {
        $opeUserId = 1;
        $currentPricePlanCode = '1-1';
        $currencyType = Enum\Model\PaymentSetting\Currency::JPY;
        $paymentSetting = [
            'payment_base_day' => 20,
            'company_country'  => 'JP',
            'currency'         => $currencyType
        ];
        GoalousDateTime::setTestNow('2017-10-21');
        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam($pricePlanGroupId = 1,
            $currentPricePlanCode, [], $paymentSetting);

        $upgradePricePlanCode = '1-2';
        $res = $this->CampaignService->upgradePlan($teamId, $upgradePricePlanCode, $opeUserId);
        $this->assertTrue($res);
        $upgradedPlanPurchased = $this->PricePlanPurchaseTeam->getByTeamId($teamId);
        $this->assertEquals($upgradedPlanPurchased['price_plan_code'], $upgradePricePlanCode);
        $history = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId);
        $baseExpected = [
            'team_id'                     => $teamId,
            'user_id'                     => $opeUserId,
            'payment_type'                => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'                 => Enum\Model\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF,
            'amount_per_user'             => 0,
            'charge_users'                => 0,
            'currency'                    => $currencyType,
            'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'campaign_team_id'            => $campaignTeamId,
            'price_plan_purchase_team_id' => $upgradedPlanPurchased['id'],
        ];

        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, Enum\Model\PaymentSetting\Currency::JPY(), $upgradePricePlanCode, $currentPricePlanCode
        );
        $expected = am($baseExpected, [
            'total_amount' => $chargeInfo['sub_total_charge'],
            'tax'          => $chargeInfo['tax'],
        ]);
        $history = array_intersect_key($history, $expected);
        $this->assertEquals($history, $expected);
    }

    function test_upgradePlan_ccForeign()
    {
        $opeUserId = 1;
        $currentPricePlanCode = '2-2';
        $currencyType = Enum\Model\PaymentSetting\Currency::USD;
        $paymentSetting = [
            'payment_base_day' => 10,
            'company_country'  => 'US',
            'currency'         => $currencyType
        ];
        GoalousDateTime::setTestNow('2017-10-21');
        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createCcCampaignTeam($pricePlanGroupId = 2,
            $currentPricePlanCode, [], $paymentSetting);

        $upgradePricePlanCode = '2-5';
        $res = $this->CampaignService->upgradePlan($teamId, $upgradePricePlanCode, $opeUserId);
        $this->assertTrue($res);
        $upgradedPlanPurchased = $this->PricePlanPurchaseTeam->getByTeamId($teamId);
        $this->assertEquals($upgradedPlanPurchased['price_plan_code'], $upgradePricePlanCode);
        $history = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId);
        $baseExpected = [
            'team_id'                     => $teamId,
            'user_id'                     => $opeUserId,
            'payment_type'                => Enum\Model\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'                 => Enum\Model\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF,
            'amount_per_user'             => 0,
            'charge_users'                => 0,
            'currency'                    => $currencyType,
            'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'campaign_team_id'            => $campaignTeamId,
            'price_plan_purchase_team_id' => $upgradedPlanPurchased['id'],
        ];

        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, Enum\Model\PaymentSetting\Currency::USD(), $upgradePricePlanCode, $currentPricePlanCode
        );
        $expected = am($baseExpected, [
            'total_amount' => $chargeInfo['sub_total_charge'],
            'tax'          => $chargeInfo['tax'],
        ]);
        $history = array_intersect_key($history, $expected);
        $this->assertEquals($history, $expected);
    }

    function test_upgradePlan_invoice()
    {
        $opeUserId = 1;
        $currentPricePlanCode = '1-3';
        $currencyType = Enum\Model\PaymentSetting\Currency::JPY;
        $paymentSetting = [
            'payment_base_day' => 12,
        ];
        GoalousDateTime::setTestNow('2017-10-21');
        list ($teamId, $campaignTeamId, $pricePlanPurchaseId) = $this->createInvoiceCampaignTeam($pricePlanGroupId = 1,
            $currentPricePlanCode, [], $paymentSetting);

        $upgradePricePlanCode = '1-4';
        $res = $this->CampaignService->upgradePlan($teamId, $upgradePricePlanCode, $opeUserId);
        $this->assertTrue($res);
        $upgradedPlanPurchased = $this->PricePlanPurchaseTeam->getByTeamId($teamId);
        $this->assertEquals($upgradedPlanPurchased['price_plan_code'], $upgradePricePlanCode);
        $history = $this->ChargeHistory->getLastChargeHistoryByTeamId($teamId);
        $baseExpected = [
            'team_id'                     => $teamId,
            'user_id'                     => $opeUserId,
            'payment_type'                => Enum\Model\PaymentSetting\Type::INVOICE,
            'charge_type'                 => Enum\Model\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF,
            'amount_per_user'             => 0,
            'charge_users'                => 0,
            'currency'                    => $currencyType,
            'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'campaign_team_id'            => $campaignTeamId,
            'price_plan_purchase_team_id' => $upgradedPlanPurchased['id'],
        ];

        $chargeInfo = $this->PaymentService->calcRelatedTotalChargeForUpgradingPlan(
            $teamId, Enum\Model\PaymentSetting\Currency::JPY(), $upgradePricePlanCode, $currentPricePlanCode
        );
        $expected = am($baseExpected, [
            'total_amount' => $chargeInfo['sub_total_charge'],
            'tax'          => $chargeInfo['tax'],
        ]);
        $history = array_intersect_key($history, $expected);
        $this->assertEquals($history, $expected);
    }

    function test_findAllPlansByGroupId_yen()
    {
        $pricePlanGroupId = 1;
        $res = $this->CampaignService->findAllPlansByGroupId($pricePlanGroupId);
        $this->assertEquals(count($res), 5);
        $this->assertEquals(reset($res), [
            'id'          => 1,
            'code'        => '1-1',
            'price'       => 50000,
            'currency'    => Enum\Model\PaymentSetting\Currency::JPY,
            'group_id'    => $pricePlanGroupId,
            'max_members' => 50,
        ]);
        $this->assertEquals(end($res), [
            'id'          => 5,
            'code'        => '1-5',
            'price'       => 250000,
            'currency'    => Enum\Model\PaymentSetting\Currency::JPY,
            'group_id'    => $pricePlanGroupId,
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
            'id'          => 6,
            'code'        => '2-1',
            'price'       => 500,
            'currency'    => Enum\Model\PaymentSetting\Currency::USD,
            'group_id'    => $pricePlanGroupId,
            'max_members' => 50,
        ]);
        $this->assertEquals(end($res), [
            'id'          => 10,
            'code'        => '2-5',
            'price'       => 2500,
            'currency'    => Enum\Model\PaymentSetting\Currency::USD,
            'group_id'    => $pricePlanGroupId,
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
