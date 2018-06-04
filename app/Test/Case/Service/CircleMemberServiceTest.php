<?php
App::uses('GoalousTestCase', 'Test');
App::uses('CircleMemberService', 'Service');

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
        $circleMemberService = new CircleMemberService();

        $result1 = $circleMemberService->getUserCircles(1, 1);

        $this->assertNotEmpty($result1);
    }
}