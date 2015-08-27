<?php
App::uses('PostLike', 'Model');

/**
 * PostLike Test Case
 *
 * @property PostLike $PostLike
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
        'app.notify_setting',
        'app.team',
        'app.local_name',
        'app.goal',
        'app.circle',
        'app.action_result',
        'app.key_result',
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

    function testChangeLike()
    {
        $this->_setDefault();
        $this->PostLike->Post->save(['team_id' => 1, 'body' => 'test']);
        $actual = $this->PostLike->changeLike($this->PostLike->Post->getLastInsertID());
        $this->assertEquals(1, $actual['count']);
        $this->assertTrue($actual['created']);
        $this->assertTrue($actual['is_liked']);

        $actual = $this->PostLike->changeLike($this->PostLike->Post->getLastInsertID());
        $this->assertEquals(0, $actual['count']);
        $this->assertFalse($actual['created']);
        $this->assertFalse($actual['is_liked']);
    }

    function testGetLikedUsers()
    {
        $this->_setDefault();
        $this->PostLike->Post->save(['team_id' => 1, 'body' => 'test']);
        $this->PostLike->changeLike($this->PostLike->Post->getLastInsertID());
        $actual = $this->PostLike->getLikedUsers($this->PostLike->Post->getLastInsertID());
        $this->assertNotEmpty($actual);
    }

    function _setDefault()
    {
        $this->PostLike->my_uid = 1;
        $this->PostLike->current_team_id = 1;
    }
}
