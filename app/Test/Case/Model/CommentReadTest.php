<?php
App::uses('CommentRead', 'Model');

/**
 * CommentRead Test Case
 *
 * @property  CommentRead $CommentRead
 */
class CommentReadTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment_read',
        'app.comment',
        'app.user', 'app.notify_setting',
        'app.team',
        'app.post',
        'app.goal',
        'app.action_result',
        'app.key_result'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->CommentRead = ClassRegistry::init('CommentRead');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CommentRead);

        parent::tearDown();
    }

    function testRed()
    {
        $uid = '1';
        $team_id = '1';
        $comment_uid = '2';
        $this->CommentRead->my_uid = $uid;
        $this->CommentRead->current_team_id = $team_id;
        $test_save_data = [
            'Post'    => [
                'user_id' => $uid,
                'team_id' => $team_id,
                'body'    => 'test',
            ],
            'Comment' => [
                [
                    'user_id' => $comment_uid,
                    'team_id' => $team_id,
                    'body'    => 'test',
                ]
            ]
        ];
        $this->CommentRead->Comment->Post->saveAll($test_save_data);
        $this->CommentRead->red($this->CommentRead->Comment->getLastInsertID());
        $comment_read = $this->CommentRead->read();
        $this->assertEquals($uid, $comment_read['CommentRead']['user_id']);

        $before_data = $comment_read;
        $this->CommentRead->red($this->CommentRead->Comment->getLastInsertID());
        $after_data = $this->CommentRead->read();
        $this->assertEquals($before_data, $after_data);

        //自分のコメントは既読にならない
        $before_data = $this->CommentRead->read();
        $my_comment = $test_save_data;
        $my_comment['Comment']['user_id'] = $uid;
        $this->CommentRead->Comment->Post->saveAll($my_comment);
        $this->CommentRead->red($this->CommentRead->Comment->getLastInsertID());
        $after_data = $this->CommentRead->read();
        $this->assertEquals($before_data, $after_data);
    }

    function testCountMyRead()
    {
        $uid = '1';
        $team_id = '1';
        $comment_uid = '2';
        $this->CommentRead->my_uid = $uid;
        $this->CommentRead->current_team_id = $team_id;
        $test_save_data = [
            'Post'    => [
                'user_id' => $uid,
                'team_id' => $team_id,
                'body'    => 'test',
            ],
            'Comment' => [
                [
                    'user_id' => $comment_uid,
                    'team_id' => $team_id,
                    'body'    => 'test',
                ]
            ]
        ];
        $this->CommentRead->Comment->Post->saveAll($test_save_data);
        $this->CommentRead->red($this->CommentRead->Comment->getLastInsertID());
        $count = $this->CommentRead->countMyRead($this->CommentRead->Comment->getLastInsertID());
        $this->assertEquals($count, 1);
    }

}
