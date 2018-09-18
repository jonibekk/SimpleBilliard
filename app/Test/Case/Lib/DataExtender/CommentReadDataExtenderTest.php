<?php
App::uses('Comment', 'Model');
App::uses('CommentRead', 'Model');
App::import('Lib/DataExtender', 'CommentReadDataExtender');
App::uses('GoalousTestCase', 'Test');

class CommentReadDataExtenderTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment',
        'app.circle',
        'app.user',
        'app.team',
        'app.local_name',
        'app.comment_read'
    );

    public function test_extendCommentRead_success()
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');
        /** @var CommentReadDataExtender $CommentReadDataExtender */
        $CommentReadDataExtender = ClassRegistry::init('CommentReadDataExtender');

        /* Not read comment */
        $comments = Hash::extract($Comment->find('first', ['conditions' => ['id' => 4]]), 'Comment');
        $this->assertNotEmpty($comments);
        $CommentReadDataExtender->setUserId(1);
        $extended = $CommentReadDataExtender->extend($comments, 'id', 'comment_id');
        $this->assertFalse(Hash::get($extended, 'is_read'));

        /* Read comment */
        $CommentRead->create();
        $CommentRead->save([
            'comment_id' => 4,
            'user_id' => 1
        ], false);
        $extended = $CommentReadDataExtender->extend($comments, 'id', 'comment_id');
        $this->assertTrue(Hash::get($extended, 'is_read'));

        /* Comment was created by logged in user */
        $comments = Hash::extract($Comment->find('all', ['conditions' => ['id' => [1,2]]]), '{n}.Comment');
        $extended = $CommentReadDataExtender->extend($comments, '{n}.id', 'comment_id');
        $ret = Hash::combine($extended, '{n}.id', '{n}.is_read');
        $this->assertEquals($ret, [1 => true, 2 => true]);

    }
}
