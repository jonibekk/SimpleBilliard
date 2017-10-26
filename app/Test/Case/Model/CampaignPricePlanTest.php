<?php
App::uses('CampaignPricePlan', 'Model');

/**
 * CampaignPricePlan Test Case
 */
class CampaignPricePlanTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
    public $fixtures = array(
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->PaymentSettingChangeLog = ClassRegistry::init('CampaignPricePlan');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown() {
        unset($this->PaymentSettingChangeLog);

        parent::tearDown();
    }

    // TODO: Implement
    function test_getMaxMemberCount() {}

    // TODO: Implement
    function test_getWithCurrency() {}

  }
