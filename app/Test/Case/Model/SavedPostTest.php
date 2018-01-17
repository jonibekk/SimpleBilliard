<?php App::uses('GoalousTestCase', 'Test');
App::uses('SavedPost', 'Model');

/**
 * SavedPost Test Case
 *
 * @property SavedPost $SavedPost
 * @property Post $Post
 * @property ActionResult $ActionResult
 */
class SavedPostTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.saved_post',
        'app.post',
        'app.action_result',
        'app.team',
        'app.goal',
        'app.key_result',
        'app.kr_progress_log',
        'app.user',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->SavedPost = ClassRegistry::init('SavedPost');
        $this->Post = ClassRegistry::init('Post');
        $this->ActionResult = ClassRegistry::init('ActionResult');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SavedPost);

        parent::tearDown();
    }

    function test_isSavedEachPost()
    {
        $userId = 1;
        $teamId = 1;
        // Argument is Empty
        $isSavedEachPost = $this->SavedPost->isSavedEachPost([], $userId);
        $this->assertEmpty($isSavedEachPost);

        // Empty record
        $isSavedEachPost = $this->SavedPost->isSavedEachPost([1], $userId);
        $this->assertNotEmpty($isSavedEachPost);
        $this->assertEquals($isSavedEachPost, [
            1 => false
        ]);

        // 1 record
        $this->SavedPost->create();
        $this->SavedPost->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'post_id' => 1,
        ]);

        $isSavedEachPost = $this->SavedPost->isSavedEachPost([1], $userId);
        $this->assertNotEmpty($isSavedEachPost);
        $this->assertEquals($isSavedEachPost, [
            1 => true
        ]);

        // multiple records
        $this->SavedPost->create();
        $this->SavedPost->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'post_id' => 2,
        ]);
        $this->SavedPost->create();
        $this->SavedPost->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'post_id' => 5,
        ]);

        $isSavedEachPost = $this->SavedPost->isSavedEachPost([1], $userId);
        $this->assertNotEmpty($isSavedEachPost);
        $this->assertEquals($isSavedEachPost, [
            1 => true
        ]);
        $isSavedEachPost = $this->SavedPost->isSavedEachPost([1,2], $userId);
        $this->assertNotEmpty($isSavedEachPost);
        $this->assertEquals($isSavedEachPost, [
            1 => true,
            2 => true
        ]);
        $isSavedEachPost = $this->SavedPost->isSavedEachPost([1,2,3,5], $userId);
        $this->assertNotEmpty($isSavedEachPost);
        $this->assertEquals($isSavedEachPost, [
            1 => true,
            2 => true,
            3 => false,
            5 => true
        ]);
        $isSavedEachPost = $this->SavedPost->isSavedEachPost([4,6,99999], $userId);
        $this->assertNotEmpty($isSavedEachPost);
        $this->assertEquals($isSavedEachPost, [
            4 => false,
            6 => false,
            99999 => false,
        ]);

        // Other use
        $isSavedEachPost = $this->SavedPost->isSavedEachPost([1,2,3,5], 2);
        $this->assertNotEmpty($isSavedEachPost);
        $this->assertEquals($isSavedEachPost, [
            1 => false,
            2 => false,
            3 => false,
            5 => false
        ]);

    }

    function test_search()
    {
        $userId = 100;
        $teamId = 1;
        // Empty record
        $res = $this->SavedPost->search($teamId, $userId, [], 0, 10);
        $this->assertEquals($res, []);

        // saved_posts record exist, but posts record doesn't exist
        $this->SavedPost->create();
        $this->SavedPost->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'post_id' => 99999999999,
        ]);
        $res = $this->SavedPost->search($teamId, $userId, [], 0, 10);
        $this->assertEquals($res, []);

        // 1 normal post record
        $this->Post->create();
        $this->Post->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'type' => Post::TYPE_NORMAL,
        ]);
        $postId = $this->Post->getLastInsertID();
        $this->SavedPost->create();
        $this->SavedPost->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'post_id' => $postId,
        ]);
        $res = $this->SavedPost->search($teamId, $userId, [], 0, 10);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['SavedPost']['post_id'], $postId);
        $this->assertEquals($res[0]['Post']['type'], Post::TYPE_NORMAL);
        $this->assertNull($res[0]['ActionResult']['id']);
        $res = $this->SavedPost->search($teamId, $userId, ['type' => Post::TYPE_NORMAL], 0, 10);
        $this->assertEquals(count($res), 1);

        $res = $this->SavedPost->search($teamId, $userId, ['type' => Post::TYPE_ACTION], 0, 10);
        $this->assertEquals($res, []);

        // 1 action post record
        $this->ActionResult->create();
        $this->ActionResult->save([
            'team_id' => $teamId,
            'user_id' => $userId,
        ]);
        $actionId = $this->ActionResult->getLastInsertID();
        $this->Post->create();
        $this->Post->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'type' => Post::TYPE_ACTION,
            'action_result_id' => $actionId,
        ]);
        $postId = $this->Post->getLastInsertID();
        $this->SavedPost->create();
        $this->SavedPost->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'post_id' => $postId,
        ]);
        $res = $this->SavedPost->search($teamId, $userId, [], 0, 10);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['SavedPost']['post_id'], $postId);
        $this->assertEquals($res[0]['Post']['type'], Post::TYPE_ACTION);
        $this->assertNotEmpty($res[0]['ActionResult']['id']);
        $savedPostId2 = $this->SavedPost->getLastInsertID();

        // different team_id, user_id
        $this->Post->create();
        $this->Post->save([
            'team_id' => 2,
            'user_id' => 2,
            'type' => Post::TYPE_NORMAL,
            'action_result_id' => $actionId,
        ]);
        $postId = $this->Post->getLastInsertID();
        $this->SavedPost->create();
        $this->SavedPost->save([
            'team_id' => 2,
            'user_id' => 2,
            'post_id' => $postId,
        ]);

        $res = $this->SavedPost->search($teamId, 2, [], 0, 10);
        $this->assertEquals($res, []);
        $res = $this->SavedPost->search(2, $userId, [], 0, 10);
        $this->assertEquals($res, []);
        $res = $this->SavedPost->search($teamId, $userId, [], 0, 10);
        $this->assertEquals(count($res), 2);

        // cursor, limit
        $res = $this->SavedPost->search($teamId, $userId, [], 0, 1);
        $this->assertEquals(count($res), 1);
        $res = $this->SavedPost->search($teamId, $userId, [], $savedPostId2, 10);
        $this->assertEquals(count($res), 1);
        $res = $this->SavedPost->search($teamId, $userId, [], $res[0]['SavedPost']['id'], 10);
        $this->assertEquals(count($res), 0);
    }
}
