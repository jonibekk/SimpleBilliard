<?php
App::uses('Comment', 'Model');

/**
 * Comment Test Case
 *
 * @property Comment $Comment
 */
class CommentTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.attached_file',
        'app.comment',
        'app.post',
        'app.user',
        'app.notify_setting',
        'app.team',
        'app.comment_like',
        'app.comment_read',
        'app.comment_file',
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
        $this->Comment = ClassRegistry::init('Comment');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Comment);

        parent::tearDown();
    }

    function testAdd()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $data = [
            'Comment' => [
                'user_id' => 1,
                'team_id' => 1,
                'post_id' => 1,
                'body'    => 'テスト',
            ]
        ];
        $this->Comment->add($data);
    }

    function testAddWithFile()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $this->Comment->CommentFile->AttachedFile = $this->getMockForModel('AttachedFile', array('saveRelatedFiles'));
        $this->Comment->CommentFile->AttachedFile->expects($this->any())
                                        ->method('saveRelatedFiles')
                                        ->will($this->returnValue(true));
        $data = [
            'Comment' => [
                'user_id' => 1,
                'team_id' => 1,
                'post_id' => 1,
                'body'    => 'テスト',
            ],
            'file_id' => ['aaaabbbbccc'],
        ];
        $this->Comment->add($data);
    }

    function testAddError()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $this->Comment->Post = $this->getMockForModel('Post', array('saveField'));
        $this->Comment->Post->expects($this->any())
                            ->method('saveField')
                            ->will($this->returnValue(false));

        $data = [
            'Comment' => [
                'user_id' => 1,
                'team_id' => 1,
                'post_id' => 1,
                'body'    => 'テスト',
            ],
        ];
        $this->Comment->add($data);
    }

    function testCommentEdit()
    {
        $data = [
            'photo_delete' => [1 => true],
            'Comment'      => [
                'id' => 1,
            ]
        ];
        $this->Comment->commentEdit($data);
    }

    function testGetCountCommentUniqueUser()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $this->Comment->getCountCommentUniqueUser(1, [1]);
    }

    function testGetCommentedUniqueUsersList()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $this->Comment->getCommentedUniqueUsersList(1);
    }

    function testGetPostsComment()
    {
        $post_id = 99;
        $this->Comment->current_team_id = 1;
        $data = [
            'team_id' => 1,
            'post_id' => $post_id,
            'body' => 'comment test.'
        ];
        $this->Comment->save($data);
        $res = $this->Comment->getPostsComment($post_id, null, 1, 'DESC');
        $this->assertNotEmpty($res);
    }

    function testConvertData()
    {
        $post_id = 99;
        $this->Comment->current_team_id = 1;
        $data = [
            'team_id' => 1,
            'post_id' => $post_id,
            'body' => 'comment test.'
        ];
        $this->Comment->save($data);
        $res = $this->Comment->getPostsComment($post_id, null, 1, 'DESC');
        $this->assertNotEmpty($this->Comment->convertData($res));
    }

    function testConvertArrayData()
    {
        $post_id = 99;
        $this->Comment->current_team_id = 1;
        $data = [
            [
                'team_id' => 1,
                'post_id' => $post_id,
                'body' => 'comment test 1.'
            ],
            [
                'team_id' => 1,
                'post_id' => $post_id,
                'body' => 'comment test 2.'
            ]
        ];
        $this->Comment->saveAll($data);
        $res = $this->Comment->getPostsComment($post_id, null, 1, 'DESC');
        $this->assertNotEmpty($this->Comment->convertData($res));
    }

}
