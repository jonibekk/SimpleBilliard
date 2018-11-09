<?php
App::uses('Circle', 'Model');
App::import('Lib/DataExtender/Extension', 'CircleMemberInfoExtension');
App::uses('GoalousTestCase', 'Test');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/09
 * Time: 15:48
 */
class CircleMemberInfoExtensionTest extends GoalousTestCase
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
        'app.circle_member'
    );

    public function test_extendManyCircleData_success()
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        $circle = $Circle->useType()->find('all', ['conditions' => ['user_id' => 1]]);
        /** @var  CircleMemberInfoExtension $CircleMemberInfoExtension */
        $CircleMemberInfoExtension = ClassRegistry::init('CircleMemberInfoExtension');
        $CircleMemberInfoExtension->setUserId(1);
        $circle = $CircleMemberInfoExtension->extendMulti($circle, '{n}.id', 'circle_id');

        foreach ($circle as $data) {
            $this->assertNotEmpty($data['unread_count']);
            $this->assertNotEmpty($data['admin_flg']);
        }
    }

    public function test_extendSingleCircleData_success()
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        $circle = $Circle->useType()->find('first', ['conditions' => ['id' => 1]])['Circle'];

        /** @var  CircleMemberInfoExtension $CircleMemberInfoExtension */
        $CircleMemberInfoExtension = ClassRegistry::init('CircleMemberInfoExtension');
        $CircleMemberInfoExtension->setUserId(1);
        $circle = $CircleMemberInfoExtension->extendMulti($circle, '{n}.id', 'circle_id');

        $this->assertNotEmpty($circle['unread_count']);
        $this->assertNotEmpty($circle['admin_flg']);
    }

    public function test_missingUserId_failed()
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        $circle = $Circle->useType()->find('first', ['conditions' => ['id' => 1]]);
        /** @var  CircleMemberInfoExtension $CircleMemberInfoExtension */
        $CircleMemberInfoExtension = ClassRegistry::init('CircleMemberInfoExtension');

        try {
            $result = $CircleMemberInfoExtension->extendMulti($circle, '{n}.id', 'circle_id');
        } catch (InvalidArgumentException $e) {

        } catch (Exception $exception) {
            $this->fail($exception);
        }
    }
}
