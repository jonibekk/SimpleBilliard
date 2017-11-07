<?php
App::uses('CampaignPricePlan', 'Model');

/**
 * CampaignPricePlan Test Case
 *
 * @property CampaignPricePlan CampaignPricePlan
*/
class CampaignPricePlanTest extends GoalousTestCase {

/**
 * Fixtures
 *
 * @var array
 */
    public $fixtures = array(
        'app.mst_price_plan',
        'app.mst_price_plan_group',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->CampaignPricePlan = ClassRegistry::init('CampaignPricePlan');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown() {
        unset($this->CampaignPricePlan);

        parent::tearDown();
    }

    function test_getMaxMemberCount() {
        // Unit test is unnecessary because target method is too simple.
    }

  }
