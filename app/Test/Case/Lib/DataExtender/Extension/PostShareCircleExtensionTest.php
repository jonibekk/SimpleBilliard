<?php
App::uses('Post', 'Model');
App::import('Lib/DataExtender/Extension', 'PostShareCircleExtension');
App::uses('GoalousTestCase', 'Test');

class PostShareCircleExtensionTest extends GoalousTestCase
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
        'app.post_share_circle',
        'app.circle_member',
        'app.team_member',
    );

    public function test_extendPostShareCircle_multi_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $posts = Hash::extract($Post->find('first', ['conditions' => ['id' => 11]]), 'Post');

        $this->assertNotEmpty($posts);

        /** @var PostShareCircleExtension $PostShareCircleExtension */
        $PostShareCircleExtension = ClassRegistry::init('PostShareCircleExtension');
        $PostShareCircleExtension->setUserId(1);
        $PostShareCircleExtension->setTeamId(1);
        $extended = $PostShareCircleExtension->extendMulti($posts, 'id', 'post_id');

        $this->assertTrue(isset($extended['shared_circles']));
        $this->assertTrue(isset($extended['shared_circles'][0]['is_member']));
    }

    public function test_extendPostShareCircle_single_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $posts = Hash::extract($Post->find('first', ['conditions' => ['id' => 11]]), 'Post');

        $this->assertNotEmpty($posts);

        /** @var PostShareCircleExtension $PostShareCircleExtension */
        $PostShareCircleExtension = ClassRegistry::init('PostShareCircleExtension');
        $PostShareCircleExtension->setUserId(1);
        $PostShareCircleExtension->setTeamId(1);
        $extended = $PostShareCircleExtension->extend($posts, 'id');

        $this->assertTrue(isset($extended['shared_circles']));
        $this->assertTrue(isset($extended['shared_circles'][0]['is_member']));
    }

}
