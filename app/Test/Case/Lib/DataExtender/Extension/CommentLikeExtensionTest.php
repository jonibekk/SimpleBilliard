<?php
App::uses('Comment', 'Model');
App::uses('CommentLike', 'Model');
App::import('Lib/DataExtender/Extension', 'CommentLikeExtension');
App::uses('GoalousTestCase', 'Test');

/**
 * @property Comment Comment
 * @property CommentLike CommentLike
 */
class CommentLikeExtensionTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post',
        'app.comment',
        'app.circle',
        'app.user',
        'app.team',
        'app.local_name',
        'app.comment_like'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Comment = ClassRegistry::init('Comment');
        $this->CommentLike = ClassRegistry::init('CommentLike');
    }


    public function test_extend_success()
    {
        $comments = Hash::extract($this->Comment->find('first', ['conditions' => ['id' => [1]]]), 'Comment');

        /** @var CommentLikeExtension $CommentLikeExtension */
        $CommentLikeExtension = ClassRegistry::init('CommentLikeExtension');
        $CommentLikeExtension->setUserId(1);

        /* User didn't like for comment */
        $extended = $CommentLikeExtension->extendMulti($comments, 'id', 'comment_id');
        $this->assertFalse(Hash::get($extended, 'is_liked'));


        /* User liked for comment */
        $this->CommentLike->create();
        $this->CommentLike->save([
            'user_id' => 1,
            'team_id' => 1,
            'comment_id' => 1,
        ], false);

        $extended = $CommentLikeExtension->extendMulti($comments, 'id', 'comment_id');
        $this->assertTrue(Hash::get($extended, 'is_liked'));
    }
}
