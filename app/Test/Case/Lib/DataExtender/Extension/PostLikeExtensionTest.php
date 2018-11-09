<?php
App::uses('Post', 'Model');
App::import('Lib/DataExtender/Extension', 'PostLikeExtension');
App::uses('GoalousTestCase', 'Test');
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/03
 * Time: 12:51
 */

class PostLikeExtensionTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post',
        'app.circle',
        'app.user',
        'app.team',
        'app.local_name',
        'app.post_like'
    );

    public function test_extendPostLike_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $posts = Hash::extract($Post->find('first', ['conditions' => ['id' => 11]]), 'Post');
        $this->assertNotEmpty($posts);
        /** @var PostLikeExtension $PostLikeExtension */
        $PostLikeExtension = ClassRegistry::init('PostLikeExtension');
        $PostLikeExtension->setUserId(1);
        $extended = $PostLikeExtension->extendMulti($posts, 'id', 'post_id');

        $this->assertTrue(Hash::get($extended, 'is_liked'));
    }
}
