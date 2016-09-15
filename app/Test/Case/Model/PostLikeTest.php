<?php App::uses('GoalousTestCase', 'Test');
App::uses('PostLike', 'Model');

/**
 * PostLike Test Case
 *
 * @property PostLike $PostLike
 */
class PostLikeTest extends GoalousTestCase
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
        'app.team',
        'app.local_name',
        'app.post_share_circle'
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

    function testChangeLikeFail()
    {
        $this->_setDefault();
        $this->PostLike->Post->save(['team_id' => 1, 'body' => 'test']);
        $last_id = $this->PostLike->Post->getLastInsertID();

        $PostLike = $this->PostLike;

        $PostLikeMock = $this->getMockForModel('PostLike', array('save'));
        /** @noinspection PhpUndefinedMethodInspection */
        $PostLikeMock->expects($this->once())
                     ->method('save')
                     ->will($this->returnValue(false));
        $this->PostLike = $PostLikeMock;
        $actual = $this->PostLike->changeLike($last_id);
        $this->assertEquals(1, $actual['error']);
        $this->assertEquals(0, $actual['count']);
        $this->assertTrue($actual['created']);
        $this->assertTrue($actual['is_liked']);

        $PostLikeMock = $this->getMockForModel('PostLike', array('save'));
        /** @noinspection PhpUndefinedMethodInspection */
        $PostLikeMock->expects($this->once())
                     ->method('save')
                     ->will($this->throwException(new PDOException()));
        $this->PostLike = $PostLikeMock;
        $actual = $this->PostLike->changeLike($last_id);
        $this->assertEquals(1, $actual['error']);
        $this->assertEquals(0, $actual['count']);
        $this->assertTrue($actual['created']);
        $this->assertTrue($actual['is_liked']);

        $this->PostLike = $PostLike;
        $actual = $this->PostLike->changeLike($last_id);
        $this->assertEquals(1, $actual['count']);
        $this->assertTrue($actual['created']);
        $this->assertTrue($actual['is_liked']);

        $actual = $this->PostLike->changeLike($last_id);
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

    function testGetCount()
    {
        $this->_setDefault();

        $now = time();
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 2, 'post_id' => 1]);
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 2]);
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 3]);
        $count = $this->PostLike->getCount([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        $this->assertEquals(3, $count);

        $count = $this->PostLike->getCount([
            'start'   => $now - HOUR,
            'end'     => $now + HOUR,
            'user_id' => 1
        ]);
        $this->assertEquals(2, $count);
    }

    function testGetUniqueUserList()
    {
        $this->_setDefault();

        $now = time();
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 1]);
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 2, 'post_id' => 1]);
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 2]);
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 3]);
        $list = $this->PostLike->getUniqueUserList([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        asort($list);
        $this->assertEquals([1 => 1, 2 => 2], $list);

        $list = $this->PostLike->getUniqueUserList([
            'start'   => $now - HOUR,
            'end'     => $now + HOUR,
            'user_id' => 1
        ]);
        asort($list);
        $this->assertEquals([1 => 1], $list);
    }

    function testGetRanking()
    {
        $this->_setDefault();

        $now = time();
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 2, 'post_id' => 1]);
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 1]);
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 2]);
        $this->PostLike->create();
        $this->PostLike->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 8]);
        $ranking = $this->PostLike->getRanking([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        $this->assertEquals(['1' => 2, '2' => 1, '8' => 1], $ranking);

        $ranking = $this->PostLike->getRanking([
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
            'limit' => 1
        ]);
        $this->assertEquals(['1' => 2], $ranking);

        $ranking = $this->PostLike->getRanking([
            'start'     => $now - HOUR,
            'end'       => $now + HOUR,
            'post_type' => Post::TYPE_ACTION
        ]);
        $this->assertEquals(['8' => 1], $ranking);

        $ranking = $this->PostLike->getRanking([
            'start'        => $now - HOUR,
            'end'          => $now + HOUR,
            'post_user_id' => 2
        ]);
        $this->assertEquals(['1' => 2], $ranking);
        $ranking = $this->PostLike->getRanking([
            'start'           => $now - HOUR,
            'end'             => $now + HOUR,
            'share_circle_id' => [1],
            'post_user_id'    => 2
        ]);
        $this->assertEquals(['1' => 2], $ranking);
        $ranking = $this->PostLike->getRanking([
            'start'           => $now - HOUR,
            'end'             => $now + HOUR,
            'share_circle_id' => [99999999],
            'post_user_id'    => 2
        ]);
        $this->assertEquals([], $ranking);
    }

    function _setDefault()
    {
        $this->PostLike->my_uid = 1;
        $this->PostLike->current_team_id = 1;
    }
}
