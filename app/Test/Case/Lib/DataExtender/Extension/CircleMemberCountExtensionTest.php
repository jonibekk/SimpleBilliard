<?php
App::uses('Post', 'Model');
App::import('Lib/DataExtender/Extension', 'CircleMemberCountExtension');
App::uses('GoalousTestCase', 'Test');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/14
 * Time: 12:08
 */
class CircleMemberCountExtensionTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.circle',
        'app.user',
        'app.team',
        'app.team_member',
        'app.circle_member',
    ];

    public function test_extendCircleMemberCountData_success()
    {
        // Originally circles table has `circle_member_count` column. but this column hasn't been maintained. So we shouldn't use this column and overwrite key value.
        $circles = [
            ['id' => 1, 'circle_member_count' => 10000],
            ['id' => 2, 'circle_member_count' => 10000],
            ['id' => 9999, 'circle_member_count' => 10000],
        ];

        /** @var CircleMemberCountExtension $CircleMemberCountExtension */
        $CircleMemberCountExtension = ClassRegistry::init('CircleMemberCountExtension');
        $extended = $CircleMemberCountExtension->extendMulti($circles, '{n}.id');
        $expect = [
            ['id' => 1, 'circle_member_count' => 3],
            ['id' => 2, 'circle_member_count' => 1],
            ['id' => 9999, 'circle_member_count' => 0],
        ];
        $this->assertEquals($extended, $expect);

        // Get from cache
        $extended = $CircleMemberCountExtension->extendMulti($circles, '{n}.id');
        $expect = [
            ['id' => 1, 'circle_member_count' => 3],
            ['id' => 2, 'circle_member_count' => 1],
            ['id' => 9999, 'circle_member_count' => 0],
        ];
        $this->assertEquals($extended, $expect);
    }
}
