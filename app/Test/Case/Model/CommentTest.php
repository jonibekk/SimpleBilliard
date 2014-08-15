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
        'app.comment',
        'app.post',
        'app.user', 'app.notify_setting',
        'app.team',
        'app.comment_like',
        'app.comment_read'
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

}
