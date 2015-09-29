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
        'app.team',
        'app.post',
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

    function testGetCount()
    {
        $this->_setDefault();

        $this->CommentLike->create();
        $this->CommentLike->save(['team_id' => 1, 'user_id' => 1, 'comment_id' => 1]);
        $this->CommentLike->create();
        $this->CommentLike->save(['team_id' => 1, 'user_id' => 2, 'comment_id' => 1]);
        $this->CommentLike->create();
        $this->CommentLike->save(['team_id' => 1, 'user_id' => 1, 'comment_id' => 2]);
        $this->CommentLike->create();
        $this->CommentLike->save(['team_id' => 1, 'user_id' => 1, 'comment_id' => 3]);

        $now = time();
        $count = $this->CommentLike->getCount(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
            ]);
        $this->assertEquals(4, $count);

        $count = $this->CommentLike->getCount(
            [
                'start'   => $now - HOUR,
                'end'     => $now + HOUR,
                'user_id' => 1,
            ]);
        $this->assertEquals(3, $count);

        $count = $this->CommentLike->getCount(
            [
                'start' => $now + HOUR,
            ]);
        $this->assertEquals(0, $count);
    }

    function testGetUniqueUserList()
    {
        $this->_setDefault();

        $now = time();
        $this->CommentLike->create();
        $this->CommentLike->save(['team_id' => 1, 'user_id' => 1, 'comment_id' => 1]);
        $this->CommentLike->create();
        $this->CommentLike->save(['team_id' => 1, 'user_id' => 2, 'comment_id' => 1]);
        $this->CommentLike->create();
        $this->CommentLike->save(['team_id' => 1, 'user_id' => 1, 'comment_id' => 1]);
        $this->CommentLike->create();
        $this->CommentLike->save(['team_id' => 1, 'user_id' => 1, 'comment_id' => 2]);
        $list = $this->CommentLike->getUniqueUserList(['start' => $now - HOUR,
                                                       'end'   => $now + HOUR]);
        asort($list);
        $this->assertEquals([1 => 1, 2 => 2], $list);

        $list = $this->CommentLike->getUniqueUserList(['start'   => $now - HOUR,
                                                       'end'     => $now + HOUR,
                                                       'user_id' => 1]);
        asort($list);
        $this->assertEquals([1 => 1], $list);
    }

    function _setDefault()
    {
        $this->CommentLike->my_uid = 1;
        $this->CommentLike->current_team_id = 1;
    }

}
