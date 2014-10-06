<?php
App::uses('Goal', 'Model');

/**
 * Goal Test Case
 *
 * @property Goal $Goal
 */
class GoalTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.goal',
        'app.key_result',
        'app.follower',
        'app.user',
        'app.team',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.comment_read',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.notify_to_user',
        'app.notify_from_user',
        'app.thread',
        'app.message',
        'app.email',
        'app.notify_setting',
        'app.oauth_token',
        'app.local_name',
        'app.goal_category'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Goal = ClassRegistry::init('Goal');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Goal);

        parent::tearDown();
    }

    function testGetMyGoals()
    {
        $this->setDefault();
        $goal_data = [
            'user_id' => 1,
            'team_id' => 1
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $key_results = [
            'goal_id'    => $goal_id,
            'team_id'    => 1,
            'start_date' => time(),
            'end_date'   => time(),
        ];
        $this->Goal->KeyResult->save($key_results);
        $this->Goal->getMyGoals();
    }

    function testGetProgress()
    {
        $goal = ['KeyResult' => []];
        $this->Goal->getProgress($goal);

        $goal = [
            'KeyResult' => [
                [
                    'priority'  => 1,
                    'completed' => null,
                ]
            ]
        ];
        $this->Goal->getProgress($goal);
        $goal = [
            'KeyResult' => [
                [
                    'priority'  => 1,
                    'completed' => 1,
                ]
            ]
        ];
        $this->Goal->getProgress($goal);
    }

    function testSortModified()
    {
        $goals = [
            [
                'Goal' => [
                    'id'       => 1,
                    'modified' => 1
                ]
            ],
            [
                'Goal' => [
                    'id'       => 2,
                    'modified' => 5
                ]
            ],
        ];
        $res = $this->Goal->sortModified($goals);
        $expected = [
            [
                'Goal' => [
                    'id'       => 2,
                    'modified' => 5
                ]
            ],
            [
                'Goal' => [
                    'id'       => 1,
                    'modified' => 1
                ]
            ]
        ];
        $this->assertEquals($expected, $res);
    }

    function testSortPriority()
    {
        $goals = [
            [
                'Goal' => [
                    'id'       => 1,
                    'priority' => 1
                ]
            ],
            [
                'Goal' => [
                    'id'       => 2,
                    'priority' => 5
                ]
            ],
        ];
        $res = $this->Goal->sortPriority($goals);
        $expected = [
            [
                'Goal' => [
                    'id'       => 2,
                    'priority' => 5
                ]
            ],
            [
                'Goal' => [
                    'id'       => 1,
                    'priority' => 1
                ]
            ],
        ];
        $this->assertEquals($expected, $res);
    }

    function testSortExistsSpecialKeyResult()
    {
        $goals = [
            [
                'Goal'             => [
                    'id' => 1,
                ],
                'SpecialKeyResult' => [
                    'id' => 1,
                ]
            ],
            [
                'Goal'             => [
                    'id' => 2,
                ],
                'SpecialKeyResult' => [
                ]
            ],
        ];
        $res = $this->Goal->sortExistsSpecialKeyResult($goals);
        $expected = [
            [
                'Goal'             => [
                    'id' => 2,
                ],
                'SpecialKeyResult' => [
                ]
            ],
            [
                'Goal'             => [
                    'id' => 1,
                ],
                'SpecialKeyResult' => [
                    'id' => 1,
                ]
            ],
        ];
        $this->assertEquals($expected, $res);
    }

    function testSortEndDate()
    {
        $goals = [
            [
                'Goal'             => [
                    'id' => 1,
                ],
                'SpecialKeyResult' => [

                ]
            ],
            [
                'Goal'             => [
                    'id' => 2,
                ],
                'SpecialKeyResult' => [
                    [
                        'end_date' => 1
                    ]
                ]
            ],
        ];
        $res = $this->Goal->sortEndDate($goals);
        $expected = [
            [
                'Goal'             => [
                    'id' => 2,
                ],
                'SpecialKeyResult' => [
                    [
                        'end_date' => 1
                    ]
                ]
            ],
            [
                'Goal'             => [
                    'id' => 1,
                ],
                'SpecialKeyResult' => [

                ]
            ],
        ];
        $this->assertEquals($expected, $res);
    }

    function setDefault()
    {
        $this->Goal->my_uid = 1;
        $this->Goal->current_team_id = 1;
        $this->Goal->Team->my_uid = 1;
        $this->Goal->Team->current_team_id = 1;
    }

}
