<?php
App::uses('PostLike', 'Model');

/**
 * PostLike Test Case
 *
 * @property mixed PostLike
 */
class PostLikeTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_like',
        'app.post',
        'app.user',
        'app.team'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostLike = ClassRegistry::init('PostLike');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostLike);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
