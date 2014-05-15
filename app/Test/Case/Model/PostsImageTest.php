<?php
App::uses('PostsImage', 'Model');

/**
 * PostsImage Test Case
 *
 * @property mixed PostsImage
 */
class PostsImageTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.posts_image',
        'app.post',
        'app.user',
        'app.team',
        //'app.goal',
        'app.comment_mention',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.given_badge',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.image',
        'app.badge',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostsImage = ClassRegistry::init('PostsImage');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostsImage);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
