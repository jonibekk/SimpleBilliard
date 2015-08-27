<?php
App::uses('CommentLike', 'Model');

/**
 * CommentLike Test Case
 *
 * @property  CommentLike $CommentLike
 */
class CommentLikeTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment_like',
        'app.comment',
        'app.user',
        'app.local_name',
        'app.notify_setting',
        'app.team',
        'app.post',
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
        $this->CommentLike = ClassRegistry::init('CommentLike');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CommentLike);

        parent::tearDown();
    }

    function testChangeLike()
    {
        $this->_setDefault();
        $this->CommentLike->Comment->save(['team_id' => 1, 'body' => 'test']);
        $actual = $this->CommentLike->changeLike($this->CommentLike->Comment->getLastInsertID());
        $this->assertEquals(1, $actual['count']);
        $this->assertTrue($actual['created']);

        $actual = $this->CommentLike->changeLike($this->CommentLike->Comment->getLastInsertID());
        $this->assertEquals(0, $actual['count']);
        $this->assertFalse($actual['created']);
    }

    function testGetLikedUsers()
    {
        $this->_setDefault();
        $this->CommentLike->Comment->save(['team_id' => 1, 'body' => 'test']);
        $this->CommentLike->changeLike($this->CommentLike->Comment->getLastInsertID());
        $actual = $this->CommentLike->getLikedUsers($this->CommentLike->Comment->getLastInsertID());
        $this->assertNotEmpty($actual);
    }

    function _setDefault()
    {
        $this->CommentLike->my_uid = 1;
        $this->CommentLike->current_team_id = 1;
    }

}
