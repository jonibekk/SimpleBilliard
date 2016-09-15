<?php App::uses('GoalousTestCase', 'Test');
App::uses('CommentRead', 'Model');

/**
 * CommentRead Test Case
 *
 * @property  CommentRead $CommentRead
 */
class CommentReadTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment_read',
        'app.comment',
        'app.user',
        'app.team',
        'app.post',
        'app.circle',
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

        $options = [
            'conditions' => [
                'comment_id' => $this->CommentRead->Comment->getLastInsertID(),
                'user_id'    => $uid
            ]
        ];
        $comment_read = $this->CommentRead->find('first', $options);
        $this->assertEquals($uid, $comment_read['CommentRead']['user_id']);

        $before_data = $comment_read;
        $this->CommentRead->red($this->CommentRead->Comment->getLastInsertID());
        $after_data = $this->CommentRead->find('first', $options);
        $this->assertEquals($before_data, $after_data);

        //自分のコメントは既読にならない
        $before_data = $this->CommentRead->find('first', $options);
        $my_comment = $test_save_data;
        $my_comment['Comment'][0]['user_id'] = $uid;
        $this->CommentRead->Comment->Post->saveAll($my_comment);
        $this->CommentRead->red($this->CommentRead->Comment->getLastInsertID());
        $after_data = $this->CommentRead->find('first', $options);
        $this->assertEquals($before_data, $after_data);
    }

    public function testRedDuplicated()
    {
        $uid = '1';
        $team_id = '1';
        $post_uid = '2';
        $this->CommentRead->my_uid = $uid;
        $this->CommentRead->current_team_id = $team_id;

        $this->CommentRead->Comment->create();
        $this->CommentRead->Comment->save(['user_id' => $post_uid, 'team_id' => $team_id, 'body' => 'test']);
        $last_id = $this->CommentRead->Comment->getLastInsertID();

        $this->CommentRead->Comment->create();
        $this->CommentRead->Comment->save(['user_id' => $post_uid, 'team_id' => $team_id, 'body' => 'test']);
        $last_id2 = $this->CommentRead->Comment->getLastInsertID();

        $res = $this->CommentRead->red($last_id, true);
        $this->assertNotEmpty($res);

        $CommentReadMock = $this->getMockForModel('CommentRead', array('pickNotMine'));
        /** @noinspection PhpUndefinedMethodInspection */
        $CommentReadMock->expects($this->any())
                        ->method('pickNotMine')
                        ->will($this->returnValue([$last_id, $last_id2]));
        $CommentReadMock->my_uid = $uid;
        $CommentReadMock->current_team_id = $team_id;
        $this->CommentRead = $CommentReadMock;
        $res = $this->CommentRead->red([$last_id, $last_id2], true);
        $this->assertNotEmpty($res);

        $res = $this->CommentRead->red([$last_id, $last_id2], true);
        $this->assertFalse($res);
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
