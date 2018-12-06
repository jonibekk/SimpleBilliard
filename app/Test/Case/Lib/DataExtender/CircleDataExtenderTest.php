<?php
App::uses('Post', 'Model');
App::import('Lib/DataExtender', 'CircleDataExtender');
App::uses('GoalousTestCase', 'Test');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/14
 * Time: 12:08
 */
class CircleDataExtenderTest extends GoalousTestCase
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
    );

    public function test_extendCircleData_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $posts = Hash::extract($Post->find('all', ['conditions' => ['circle_id is not null']]), '{n}.Post');
        $this->assertNotEmpty($posts);
        /** @var CircleDataExtender $CircleDataExtender */
        $CircleDataExtender = ClassRegistry::init('CircleDataExtender');
        $extended = $CircleDataExtender->extend($posts, '{n}.circle_id');
        $this->assertNotEmpty(Hash::extract($extended, '{n}.circle'));
    }
}