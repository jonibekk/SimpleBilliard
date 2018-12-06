<?php
App::uses('Post', 'Model');
App::uses('PostRead', 'Model');
App::import('Lib/DataExtender', 'PostReadDataExtender');
App::uses('GoalousTestCase', 'Test');

class PostReadDataExtenderTest extends GoalousTestCase
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
        'app.post_read'
    );

    public function test_extendPostRead_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');
        /** @var PostReadDataExtender $PostReadDataExtender */
        $PostReadDataExtender = ClassRegistry::init('PostReadDataExtender');

        /* Not read post */
        $posts = Hash::extract($Post->find('first', ['conditions' => ['id' => 11]]), 'Post');
        $this->assertNotEmpty($posts);
        $PostReadDataExtender->setUserId(1);
        $extended = $PostReadDataExtender->extend($posts, 'id', 'post_id');
        $this->assertFalse(Hash::get($extended, 'is_read'));

        /* Read post */
        $PostRead->create();
        $PostRead->save([
            'post_id' => 11,
            'user_id' => 1
        ], false);
        $extended = $PostReadDataExtender->extend($posts, 'id', 'post_id');
        $this->assertTrue(Hash::get($extended, 'is_read'));

        /* Post was created by logged in user */
        $posts = Hash::extract($Post->find('all', ['conditions' => ['id' => [7,8]]]), '{n}.Post');
        $extended = $PostReadDataExtender->extend($posts, '{n}.id', 'post_id');
        $ret = Hash::combine($extended, '{n}.id', '{n}.is_read');
        $this->assertEquals($ret, [7 => true, 8 => true]);

    }
}
