<?php
App::uses('GlRedis', 'Model');

/**
 * GlRedis Test Case
 *
 * @property GlRedis $GlRedis
 */
class GlRedisTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array();

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GlRedis = ClassRegistry::init('GlRedis');
        $this->GlRedis->changeDbSource('redis_test');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->GlRedis->deleteAllData();
        unset($this->GlRedis);
        parent::tearDown();
    }

    function testSetNotifications()
    {
        $res = $this->GlRedis->setNotifications(1, 2000000, [2], 1, "body", ['/'], time());
        $this->assertTrue($res);

    }

}
