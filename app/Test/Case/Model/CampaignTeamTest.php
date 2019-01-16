<?php
App::uses('CampaignTeam', 'Model');
use Goalous\Enum as Enum;

/**
 * CampaignTeam Test Case
 *
 * @property CampaignTeam CampaignTeam
*/
class CampaignTeamTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
    public $fixtures = array(
        'app.campaign_team',
        'app.view_price_plan',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->CampaignTeam = ClassRegistry::init('CampaignTeam');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown() {
        unset($this->CampaignTeam);

        parent::tearDown();
    }

    function test_isCampaignTeam() {
        // Not exist
        $teamId = 1;
        $res = $this->CampaignTeam->isCampaignTeam($teamId);
        $this->assertFalse($res);

        // Exist
        $this->CampaignTeam->save([
            'team_id' => $teamId,
            'start_date' => GoalousDateTime::now()->toIso8601String(),
        ]);
        $res = $this->CampaignTeam->isCampaignTeam($teamId);
        $this->assertTrue($res);
    }

    function test_findPricePlans() {
        // Not exist campaign team
        $teamId = 1;
        $res = $this->CampaignTeam->findPricePlans($teamId);
        $this->assertEmpty($res);

        // Exist campaign team
        $this->CampaignTeam->save([
            'team_id' => $teamId,
            'price_plan_group_id' => 1
        ]);

        $res = $this->CampaignTeam->findPricePlans($teamId);
        $this->assertNotEmpty($res);
        $pricePlan = reset($res);
        $expected = ['id' =>  1, 'group_id' => 1, 'code' =>  '1-1', 'price' =>  50000, 'max_members' =>  50, 'currency' => 1];
        $this->assertEquals($pricePlan['id'], $expected['id']);
        $this->assertEquals($pricePlan['code'], $expected['code']);
        $this->assertEquals($pricePlan['price'], $expected['price']);
        $this->assertEquals($pricePlan['max_members'], $expected['max_members']);
        $this->assertEquals($pricePlan['currency'], $expected['currency']);

        // Other price plan group
        $this->CampaignTeam->clear();
        $this->CampaignTeam->id = $this->CampaignTeam->getLastInsertID();
        $this->CampaignTeam->save([
            'price_plan_group_id' => 2
        ]);

        $res = $this->CampaignTeam->findPricePlans($teamId);
        $this->assertNotEmpty($res);
        $pricePlan = reset($res);
        $expected = ['id' =>  6, 'group_id' => 2, 'code' =>  '2-1', 'price' =>  500,   'max_members' =>  50, 'currency' => 2];
        $this->assertEquals($pricePlan['id'], $expected['id']);
        $this->assertEquals($pricePlan['code'], $expected['code']);
        $this->assertEquals($pricePlan['price'], $expected['price']);
        $this->assertEquals($pricePlan['max_members'], $expected['max_members']);
        $this->assertEquals($pricePlan['currency'], $expected['currency']);

    }

    function test_isTeamPricePlan() {
        // Not exist campaign team
        $teamId = 1;
        $pricePlanCode = '1-1';
        $res = $this->CampaignTeam->isTeamPricePlan($teamId, $pricePlanCode);
        $this->assertFalse($res);

        // Exist campaign team
        $this->CampaignTeam->save([
            'team_id' => $teamId,
            'price_plan_group_id' => 1
        ]);

        $res = $this->CampaignTeam->isTeamPricePlan($teamId, $pricePlanCode);
        $this->assertTrue($res);

        $pricePlanCode = "2-1";
        $res = $this->CampaignTeam->isTeamPricePlan($teamId, $pricePlanCode);
        $this->assertFalse($res);

    }

  }
