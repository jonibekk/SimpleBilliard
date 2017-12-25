<?php App::uses('GoalousTestCase', 'Test');
App::uses('SavedPost', 'Model');

/**
 * SavedPost Test Case
 *
 * @property SavedPost $SavedPost
 * @property Post $Post
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
        'app.team',
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

}
