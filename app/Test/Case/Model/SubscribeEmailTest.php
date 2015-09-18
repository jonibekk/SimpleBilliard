<?php
App::uses('SubscribeEmail', 'Model');

/**
 * SubscribeEmail Test Case
 *
 * @property SubscribeEmail $SubscribeEmail
 */
class SubscribeEmailTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.subscribe_email'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->SubscribeEmail = ClassRegistry::init('SubscribeEmail');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SubscribeEmail);

        parent::tearDown();
    }

    function testDummy()
    {
    }

}
