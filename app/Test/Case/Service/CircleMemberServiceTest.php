<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'CircleMemberService');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/04
 * Time: 15:59
 */
class CircleMemberServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.circle',
        'app.user',
        'app.circle_member',
        'app.team'
    ];

    public function test_fetchCircles_success()
    {
        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');

        $result1 = $CircleMemberService->getUserCircles(1, 1);

        $this->assertNotEmpty($result1);
    }
}