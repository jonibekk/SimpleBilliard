<?php
App::uses('CampaignPriceGroup', 'Model');

/**
 * CampaignPriceGroup Test Case
 */
class CampaignPriceGroupTest extends CakeTestCase {

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
        $this->PaymentSettingChangeLog = ClassRegistry::init('CampaignPriceGroup');
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
    function test_getCurrency() {}
}
