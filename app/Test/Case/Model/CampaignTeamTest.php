<?php
App::uses('CampaignTeam', 'Model');

/**
 * CampaignTeam Test Case
 */
class CampaignTeamTest extends CakeTestCase {

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
        $this->PaymentSettingChangeLog = ClassRegistry::init('CampaignTeam');
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
    function test_isCampaignTeam() {}

    // TODO: Implement
    function test_findPricePlans() {}

    // TODO: Implement
    function test_isTeamPricePlan() {}

  }
