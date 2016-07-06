<?php
App::uses('AppMeta', 'Model');
App::uses('GoalousTestCase', 'Test');
/**
 * AppMeta Test Case
 * @property AppMeta $AppMeta
 */
class AppMetaTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.app_meta'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->AppMeta = ClassRegistry::init('AppMeta');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AppMeta);

        parent::tearDown();
    }

}
