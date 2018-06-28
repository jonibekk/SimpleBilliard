<?php
App::uses('Post', 'Model');
App::import('Lib/DataExtender','UserDataExtender');
App::uses('GoalousTestCase', 'Test');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/14
 * Time: 10:48
 */
class UserDataExtenderTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post',
        'app.user',
        'app.team',
        'app.local_name',
    );

    public function test_extendUserData_success()
    {
        $Post = new Post();
        $posts = Hash::extract($Post->find('all', ['conditions' => ['user_id' => '1']]), '{n}.Post');

        $UserDataExtender = new UserDataExtender();
        $extended = $UserDataExtender->extend($posts, '{n}.user_id');

        $this->assertNotEmpty(Hash::extract($extended, '{n}.user'));
    }
}