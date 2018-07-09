<?php
App::uses('Post', 'Model');
App::import('Lib/DataExtender', 'PostSavedDataExtender');
App::uses('GoalousTestCase', 'Test');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/03
 * Time: 12:52
 */
class PostSavedDataExtenderTest extends GoalousTestCase
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
        'app.saved_post'
    );

    public function test_extendPostLike_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $posts = Hash::extract($Post->find('first', ['conditions' => ['id' => 11]]), 'Post');
        $this->assertNotEmpty($posts);
        /** @var PostSavedDataExtender $PostSavedDataExtender */
        $PostSavedDataExtender = ClassRegistry::init('PostSavedDataExtender');
        $PostSavedDataExtender->setUserId(1);
        $extended = $PostSavedDataExtender->extend($posts, 'id', 'post_id');

        $this->assertNotEmpty(Hash::extract($extended, 'is_saved'));
    }

}