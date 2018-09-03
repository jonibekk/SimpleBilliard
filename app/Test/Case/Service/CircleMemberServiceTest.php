<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'CircleMemberService');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/04
 * Time: 15:59
 */

use Goalous\Exception as GlException;

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
        'app.team',
        'app.post_share_circle'
    ];

    public function test_fetchCircles_success()
    {
        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');

        $result1 = $CircleMemberService->getUserCircles(1, 1);

        $this->assertNotEmpty($result1);
    }

    public function test_add_success()
    {
        $newCircleId = 2;
        $newUserId = 2;
        $newTeamId = 1;

        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');

        $result = $CircleMemberService->add($newUserId, $newCircleId, $newTeamId);

        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result['id']);
        $this->assertEquals($newCircleId, $result['circle_id']);
        $this->assertEquals($newUserId, $result['user_id']);
        $this->assertEquals($newTeamId, $result['team_id']);
    }

    /**
     * @expectedException  GlException\GoalousConflictException
     */
    public function test_addAlreadyExist_failed()
    {
        $newCircleId = 2;
        $newUserId = 2;
        $newTeamId = 1;

        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');

        $result = $CircleMemberService->add($newUserId, $newCircleId, $newTeamId);
        $result = $CircleMemberService->add($newUserId, $newCircleId, $newTeamId);
    }

    /**
     * @expectedException GlException\GoalousNotFoundException
     */
    public function test_addCircleNotExist_failed()
    {
        $newCircleId = 123123;
        $newUserId = 2;
        $newTeamId = 1;

        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');

        $result = $CircleMemberService->add($newUserId, $newCircleId, $newTeamId);
    }
}