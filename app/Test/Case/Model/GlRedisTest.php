<?php
App::uses('GlRedis', 'Model');

/**
 * GlRedis Test Case
 * @property GlRedis $GlRedis

 */
class GlRedisTest extends CakeTestCase
{

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
    public function setUp()
    {
        parent::setUp();
        $this->GlRedis = ClassRegistry::init('GlRedis');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GlRedis);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
