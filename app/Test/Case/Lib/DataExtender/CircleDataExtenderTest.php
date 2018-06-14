<?php
App::uses('Post', 'Model');
App::uses('CircleDataExtender', 'Lib/DataExtender');
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
        $Post = new Post();
        $posts = Hash::extract($Post->find('all', ['conditions' => ['circle_id is not null']]), '{n}.Post');

        $CircleDataExtender = new CircleDataExtender();
        $extended = $CircleDataExtender->extend($posts, '{n}.circle_id');

        $this->assertNotEmpty(Hash::extract($extended, '{n}.circle'));
    }
}