<?php
App::uses('Post', 'Model');
App::import('Lib/DataExtender/Extension', 'CircleExtension');
App::uses('GoalousTestCase', 'Test');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/14
 * Time: 12:08
 */
class CircleExtensionTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.post',
        'app.circle',
        'app.user',
        'app.team',
        'app.local_name',
    ];

    public function test_extendCircleData_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $posts = Hash::extract($Post->find('all', ['conditions' => ['circle_id is not null']]), '{n}.Post');
        $this->assertNotEmpty($posts);
        /** @var CircleExtension $CircleExtension */
        $CircleExtension = ClassRegistry::init('CircleExtension');
        $extended = $CircleExtension->extendMulti($posts, '{n}.circle_id');
        $this->assertNotEmpty(Hash::extract($extended, '{n}.circle'));
    }
}
