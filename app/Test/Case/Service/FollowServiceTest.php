<?php

App::uses('GoalousTestCase', 'Test');
App::import('Service', 'FollowService');

/**
 * FollowServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property FollowService $FollowService
 * @property Goal $Goal
 * @property GoalMember $GoalMember
 * @property Team $Team
 * @property TeamMember $TeamMember
 * @property Term $Term
 */
class FollowServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.goal',
        'app.goal_member',
        'app.team',
        'app.team_member',
        'app.term',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->FollowService = ClassRegistry::init('FollowService');
        $this->Goal = ClassRegistry::init('Goal');
        $this->GoalMember = ClassRegistry::init('GoalMember');
        $this->Team = ClassRegistry::init('Team');
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->Term = ClassRegistry::init('Term');
    }

    function testAdd()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testDelete()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    /**
     * @return array
     */
    public function providerValidateToFollow(): array
    {
        return [
            'The Goal doesn\'t exist.' => [
                'teamId' => 1,
                'goalId' => 0,
                'userId' => 1,
                'message' => __('The Goal doesn\'t exist.'),
            ],
            'You cannot follow or collaborate with a completed Goal.' => [
                'teamId' => 1,
                'goalId' => 1,
                'userId' => 1,
                'message' => __('You cannot follow or collaborate with a completed Goal.')
            ],
            'You cannot follow or collaborate with a past Goal.' => [
                'teamId' => 2,
                'goalId' => 2,
                'userId' => 1,
                'message' => __('You cannot follow or collaborate with a past Goal.')
            ],
            'You cannot follow because you are participating in collaboration.' => [
                'teamId' => 1,
                'goalId' => 3,
                'userId' => 1,
                'message' => __('You cannot follow because you are participating in collaboration.')
            ],
            'You cannot follow because you are coaching this goal.' => [
                'teamId' => 3,
                'goalId' => 4,
                'userId' => 1,
                'message' => __('You cannot follow because you are coaching this goal.')
            ]
        ];
    }

    /**
     * @param int $teamId
     * @param int $goalId
     * @param int $userId
     * @param string $message
     * @group validateToFollow
     * @dataProvider providerValidateToFollow
     * @expectedException \Goalous\Exception\Follow\ValidationToFollowException
     * @throws Throwable
     */
    public function testValidateToFollow(int $teamId, int $goalId, int $userId, string $message)
    {
        $this->Goal->getDataSource()->truncate('goals');
        $this->GoalMember->getDataSource()->truncate('goal_members');
        $this->Term->current_team_id = $teamId;
        $this->Team->current_team_id = $teamId;
        $this->TeamMember->current_team_id = $teamId;
        $this->GoalMember->current_team_id = $teamId;

        $this->createValidateToFollowTestData();
        try {
            $this->FollowService->validateToFollow($teamId, $goalId, $userId);
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\Goalous\Exception\Follow\ValidationToFollowException::class, $e);
            $this->assertSame($message, $e->getMessage());
            throw $e;
        }
    }

    private function createValidateToFollowTestData(): void
    {
        // Create completed goal data.
        $this->Goal->id = 1;
        $this->Goal->save([
            'user_id' => 1,
            'team_id' => 1,
            'name' => 'hogehoge',
            'goal_category_id' => 1,
            'start_date' => '2019-11-13',
            'end_date' => '2099-12-31',
            'completed' => time()
        ]);
        $this->Term->create();
        $this->Term->save([
            'team_id' => 1,
            'start_date' => '2019-01-01',
            'end_date' => '2099-12-31',
        ]);

        // Create finished goal.
        $this->Goal->id = 2;
        $this->Goal->save([
            'user_id' => 1,
            'team_id' => 2,
            'name' => 'hogehoge',
            'goal_category_id' => 1,
            'start_date' => '2019-01-01',
            'end_date' => '2019-11-10',
        ]);
        $this->Term->create();
        $this->Term->save([
            'team_id' => 2,
            'start_date' => '2019-01-01',
            'end_date' => '2099-12-31',
        ]);

        // Create goal with collaboration.
        $this->Goal->id = 3;
        $goal = $this->Goal->save([
            'user_id' => 1,
            'team_id' => 1,
            'name' => 'hogehoge',
            'goal_category_id' => 1,
            'start_date' => '2019-11-13',
            'end_date' => '2099-12-31',
        ]);
        $this->GoalMember->create();
        $this->GoalMember->save([
            'team_id' => 1,
            'goal_id' => $goal['Goal']['id'],
            'user_id' => 1,
            'type' => GoalMember::TYPE_COLLABORATOR
        ]);

        // Create goal with coaching
        $this->Goal->id = 4;
        $goal = $this->Goal->save([
            'user_id' => 2,
            'team_id' => 3,
            'name' => 'hogehoge',
            'goal_category_id' => 1,
            'start_date' => '2019-11-13',
            'end_date' => '2099-12-31',
        ]);
        $this->GoalMember->create();
        $this->GoalMember->save([
            'team_id' => 3,
            'goal_id' => $goal['Goal']['id'],
            'user_id' => 2,
        ]);
        $this->TeamMember->save([
            'user_id' => 2,
            'team_id' => 3,
            'coach_user_id' => 1,
        ]);
        $this->Term->create();
        $this->Term->save([
            'team_id' => 3,
            'start_date' => '2019-01-01',
            'end_date' => '2099-12-31',
        ]);
    }

    function testGetUnique()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

}
