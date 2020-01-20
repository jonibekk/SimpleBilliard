<?php
App::uses('GoalousTestCase', 'Test');
App::uses('UnreadCirclePost', 'Model');

class UnreadCirclePostTest extends GoalousTestCase
{
    public $fixtures = [
        'app.cache_unread_circle_post'
    ];

    public function test_addSingle_success()
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');
        $UnreadCirclePost->add(1, 1, 1, 1);

        $this->assertEquals(1, $UnreadCirclePost->find('count', []));
    }

    public function test_addMany_success()
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $UnreadCirclePost->addMany(1, 1, [2, 3, 4], 1);

        $this->assertEquals(3, $UnreadCirclePost->find('count', []));
    }

    public function test_count_success()
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $this->createData();

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 1);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle(2, 5);
        $this->assertEquals(1, $count);

        $count = $UnreadCirclePost->countPostUnread(1, 1);
        $this->assertEquals(4, $count);

        $count = $UnreadCirclePost->countPostUnread(2, 3);
        $this->assertEquals(3, $count);
    }

    public function test_getPosts_success()
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $this->createData();

        $postIds = $UnreadCirclePost->getPostIdsInCircle(1, 1);

        $this->assertEquals([1, 2], $postIds);
    }

    public function test_deleteSingle_success()
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $this->createData();

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 1);
        $this->assertEquals(2, $count);

        $UnreadCirclePost->deleteSinglePost(1, 1, 1);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 1);
        $this->assertEquals(1, $count);
    }

    public function test_deleteByCircleUser_success()
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $this->createData();

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 1);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 2);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countPostUnread(1, 1);
        $this->assertEquals(4, $count);

        $UnreadCirclePost->deleteCircleUser(1, 1);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 1);
        $this->assertEquals(0, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 2);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countPostUnread(1, 1);
        $this->assertEquals(3, $count);
    }

    public function test_deleteInManyCirclesUser_success()
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $this->createData();

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 2);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle(2, 2);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countPostUnread(1, 1);
        $this->assertEquals(4, $count);

        $UnreadCirclePost->deleteByTeamUser(1, 2);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 1);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 2);
        $this->assertEquals(0, $count);

        $count = $UnreadCirclePost->countPostUnread(1, 1);
        $this->assertEquals(3, $count);
    }

    public function test_deleteAllByPost_success()
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $this->createData();

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 1);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 2);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countPostUnread(1, 1);
        $this->assertEquals(4, $count);

        $UnreadCirclePost->deleteAllByPost(1);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 1);
        $this->assertEquals(1, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 2);
        $this->assertEquals(1, $count);

        $count = $UnreadCirclePost->countPostUnread(1, 1);
        $this->assertEquals(0, $count);

        $count = $UnreadCirclePost->countPostUnread(1, 2);
        $this->assertEquals(4, $count);
    }

    public function test_deleteAllByCircle_success()
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $this->createData();

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 1);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle(2, 3);
        $this->assertEquals(1, $count);

        $count = $UnreadCirclePost->countPostUnread(1, 1);
        $this->assertEquals(4, $count);

        $UnreadCirclePost->deleteAllByCircle(1);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 1);
        $this->assertEquals(0, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle(1, 2);
        $this->assertEquals(0, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle(2, 3);
        $this->assertEquals(1, $count);

        $count = $UnreadCirclePost->countPostUnread(2, 3);
        $this->assertEquals(3, $count);
    }

    private function createData()
    {
        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        $UnreadCirclePost->addMany(1, 1, [1, 2, 3, 4], 1);
        $UnreadCirclePost->addMany(1, 1, [1, 2, 3, 4], 2);
        $UnreadCirclePost->addMany(1, 2, [1, 2, 5], 3);
        $UnreadCirclePost->addMany(1, 2, [1, 3, 6], 4);
    }
}
