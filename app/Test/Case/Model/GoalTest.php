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
        'app.approval_history',
        'app.action_result',
        'app.evaluation',
        'app.evaluate_term',
        'app.purpose',
        'app.goal',
        'app.key_result',
        'app.collaborator',
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
            'user_id'    => 1,
            'team_id'    => 1,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $key_results = [
            'goal_id'    => $goal_id,
            'team_id'    => 1,
            'user_id'    => 1,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ];
        $this->Goal->KeyResult->save($key_results);
        $this->Goal->getMyGoals();
    }

    function testGetMyGoalsWithNoGoalPurpose()
    {
        $this->setDefault();
        $goal_data = [
            'user_id'    => 1,
            'team_id'    => 1,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $key_results = [
            'goal_id'    => $goal_id,
            'team_id'    => 1,
            'user_id'    => 1,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ];
        $this->Goal->KeyResult->save($key_results);
        $purpose = [
            'name'    => 'test',
            'user_id' => 1,
            'team_id' => 1,
        ];
        $this->Goal->Purpose->save($purpose);
        $this->Goal->getMyGoals();
    }

    function testGetAllGoals()
    {
        $this->setDefault();
        $goal_data = [
            'user_id'    => 1,
            'team_id'    => 1,
            'purpose_id' => 1,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $key_results = [
            'goal_id'     => $goal_id,
            'team_id'     => 1,
            'user_id'     => 1,
            'special_flg' => true,
            'start_date'  => $this->start_date,
            'end_date'    => $this->end_date,
        ];
        $this->Goal->KeyResult->create();
        $this->Goal->KeyResult->save($key_results);
        $res = $this->Goal->getAllGoals();
        $this->assertTrue(!empty($res));
    }

    function testGetByGoalId()
    {
        $this->setDefault();
        $goal_data = [
            'user_id'    => 1,
            'team_id'    => 1,
            'purpose_id' => 1,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $res = $this->Goal->getByGoalId($goal_id);
        $this->assertTrue(!empty($res));
    }

    function testGetProgress()
    {
        $goal = ['KeyResult' => []];
        $this->Goal->getProgress($goal);

        $goal = [
            'KeyResult' => [
                [
                    'priority' => 1,
                    'progress' => 0,
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
                'Goal'      => [
                    'id' => 1,
                ],
                'MyCollabo' => [
                    ['priority' => 1]
                ]
            ],
            [
                'Goal'      => [
                    'id' => 2,
                ],
                'MyCollabo' => [
                    ['priority' => 5]
                ]
            ],
        ];
        $res = $this->Goal->sortPriority($goals);
        $expected = [
            [
                'Goal'      => [
                    'id' => 2,
                ],
                'MyCollabo' => [
                    ['priority' => 5]
                ]
            ],
            [
                'Goal'      => [
                    'id' => 1,
                ],
                'MyCollabo' => [
                    ['priority' => 1]
                ]
            ],
        ];
        $this->assertEquals($expected, $res);
    }

    function testSortEndDate()
    {
        $goals = [
            [
                'Goal' => [
                    'id' => 1,
                ],
            ],
            [
                'Goal' => [
                    'id'       => 2,
                    'end_date' => 1,
                ],
            ],
        ];
        $res = $this->Goal->sortEndDate($goals);
        $expected = [
            [
                'Goal' => [
                    'id'       => 2,
                    'end_date' => 1,
                ],
            ],
            [
                'Goal' => [
                    'id' => 1,
                ],
            ],
        ];
        $this->assertEquals($expected, $res);
    }

    function testGetAddData()
    {
        $this->setDefault();
        $goal_id = $this->_getNewGoal();
        $this->Goal->getAddData($goal_id);
    }

    function testAddFail()
    {
        $this->setDefault();
        $this->Goal->add([]);
    }

    function testCompleteSuccess()
    {
        $this->setDefault();
        $goal_id = $this->_getNewGoal();
        $this->Goal->complete($goal_id);
    }

    function testCompleteFail()
    {
        $this->setDefault();
        try {
            $this->Goal->complete(null);
        } catch (RuntimeException $e) {
        }
    }

    function testAddNewSuccess()
    {
        $this->setDefault();
        $data = [
            'Goal' => [
                'purpose_id'       => 1,
                'goal_category_id' => 1,
                'name'             => 'test',
                'value_unit'       => 0,
                'target_value'     => 100,
                'start_value'      => 0,
                'start_date'       => $this->start_date,
                'end_date'         => $this->end_date,
            ]
        ];
        $this->Goal->add($data);
    }

    function testAddNewSuccessUnitValue()
    {
        $this->setDefault();
        $data = [
            'Goal' => [
                'purpose_id'       => 1,
                'goal_category_id' => 1,
                'name'             => 'test',
                'value_unit'       => 2,
                'target_value'     => 100,
                'start_value'      => 0,
                'start_date'       => $this->start_date,
                'end_date'         => $this->end_date,
            ]
        ];
        $this->Goal->add($data);
    }

    function _getNewGoal()
    {
        $goal = [
            'user_id'    => 1,
            'team_id'    => 1,
            'name'       => 'test',
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ];
        $this->Goal->create();
        $this->Goal->save($goal);
        $goal_id = $this->Goal->getLastInsertID();
        $kr = [
            'user_id'    => 1,
            'team_id'    => 1,
            'goal_id'    => $goal_id,
            'name'       => 'test',
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ];
        $this->Goal->KeyResult->create();
        $this->Goal->KeyResult->save($kr);
        $collabo = [
            'user_id' => 1,
            'team_id' => 1,
            'goal_id' => $goal_id,
        ];
        $this->Goal->Collaborator->create();
        $this->Goal->Collaborator->save($collabo);
        return $goal_id;
    }

    function testIncompleteFail()
    {
        try {
            $this->Goal->incomplete(null);
        } catch (Exception $e) {
        }
        $this->assertTrue(isset($e));
    }

    var $current_date;
    var $start_date;
    var $end_date;

    function setDefault()
    {
        $this->current_date = strtotime('2015/7/1');
        $this->start_date = strtotime('2015/7/1');
        $this->end_date = strtotime('2015/10/1');

        $this->Goal->Team->current_term_start_date = strtotime('2015/1/1');
        $this->Goal->Team->current_term_end_date = strtotime('2015/12/1');
        $this->Goal->my_uid = 1;
        $this->Goal->current_team_id = 1;
        $this->Goal->Purpose->my_uid = 1;
        $this->Goal->Purpose->current_team_id = 1;
        $this->Goal->Team->my_uid = 1;
        $this->Goal->Team->current_team_id = 1;
        $this->Goal->KeyResult->my_uid = 1;
        $this->Goal->KeyResult->current_team_id = 1;
        $this->Goal->Collaborator->my_uid = 1;
        $this->Goal->Collaborator->current_team_id = 1;
        $this->Goal->Post->my_uid = 1;
        $this->Goal->Post->current_team_id = 1;
        $this->Goal->Evaluation->current_team_id = 1;
        $this->Goal->Evaluation->my_uid = 1;
        $this->Goal->Team->EvaluateTerm->current_team_id = 1;
        $this->Goal->Team->EvaluateTerm->my_uid = 1;

    }

    function testGetGoalIdFromUserId()
    {
        $this->setDefault();
        $user_id = 1;
        $team_id = 1;
        $goal_params = [
            'user_id'    => $user_id,
            'team_id'    => $team_id,
            'name'       => 'test',
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ];
        $this->Goal->save($goal_params);
        $goal_id = $this->Goal->getLastInsertID();
        $res = $this->Goal->getGoalIdFromUserId($user_id, $team_id);
        $this->assertContains($goal_id, $res);
    }

    function testIsNotExistsEvaluation()
    {
        $this->setDefault();
        $save_data = [
            'goal_id'           => 1,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 1,
            'team_id'           => 1,
        ];
        $this->Goal->Evaluation->save($save_data);
        try {
            $this->Goal->isNotExistsEvaluation(1);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testSetFilter()
    {
        $this->setDefault();
        $options = $this->Goal->getSearchOptions();
        foreach ($options as $type => $val) {
            foreach ($val as $key => $value) {
                $search_option[$type][0] = $key;
                $this->Goal->getAllGoals(null, $search_option);
            }
        }
    }

    function testGoalFilterTermNoData()
    {
        $this->setDefault();
        $search_options = [];
        $search_options['term'] = ['previous'];
        $this->Goal->setFilter([], $search_options);
        $search_options['term'] = ['next'];
        $this->Goal->setFilter([], $search_options);
        $search_options['term'] = ['before'];
        $this->Goal->setFilter([], $search_options);
    }

    function testGoalFilterTermNoExistsData()
    {
        $this->setDefault();
        $current = $this->Goal->Team->EvaluateTerm->saveCurrentTerm();
        $this->Goal->Team->EvaluateTerm->saveNextTerm();
        $end = $current['EvaluateTerm']['start_date'] - 1;
        $start = $current['EvaluateTerm']['start_date'] - 1000000;
        $this->Goal->Team->EvaluateTerm->saveTerm($start, $end);

        $search_options = [];
        $search_options['term'] = ['previous'];
        $this->Goal->setFilter([], $search_options);
        $search_options['term'] = ['next'];
        $this->Goal->setFilter([], $search_options);
        $search_options['term'] = ['before'];
        $this->Goal->setFilter([], $search_options);
    }

    function testGetMyPreviousGoals()
    {
        $this->setDefault();
        $term = $this->Goal->Team->getBeforeTermStartEnd(1);
        $goal_data = [
            'user_id'    => 1,
            'team_id'    => 1,
            'purpose_id' => 1,
            'start_date' => $term['start'] + 1,
            'end_date'   => $term['end'] - 1,
        ];
        $this->Goal->create();
        $this->Goal->save($goal_data);
        $goal_data = [
            'user_id'    => 2,
            'team_id'    => 1,
            'purpose_id' => 1,
            'start_date' => $term['start'] + 1,
            'end_date'   => $term['end'] - 1,
        ];
        $this->Goal->create();
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $collabo = [
            'user_id' => 1,
            'team_id' => 1,
            'goal_id' => $goal_id,
        ];
        $key_results = [
            'goal_id'    => $goal_id,
            'team_id'    => 1,
            'user_id'    => 1,
            'start_date' => $this->start_date + 1,
            'end_date'   => $this->end_date - 1,
        ];
        $this->Goal->KeyResult->save($key_results);
        $purpose = [
            'name'    => 'test',
            'user_id' => 1,
            'team_id' => 1,
        ];
        $this->Goal->Purpose->save($purpose);
        $this->Goal->KeyResult->save($key_results);
        $this->Goal->Collaborator->create();
        $this->Goal->Collaborator->save($collabo);
        $this->Goal->getMyPreviousGoals();
    }

    function testIsPresentTermGoalPatternTrue()
    {

        $this->Goal->Team->current_term_start_date = strtotime('2015/1/1');
        $this->Goal->Team->current_term_end_date = strtotime('2015/12/1');

        $goal_data = [
            'user_id'    => 1,
            'team_id'    => 1,
            'purpose_id' => 1,
            'start_date' => strtotime('2015/2/1'),
            'end_date'   => strtotime('2015/3/1'),
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();

        $res = $this->Goal->isPresentTermGoal($goal_id);
        $this->assertTrue($res);
    }

    function testIsPresentTermGoalPatternFalse()
    {

        $this->Goal->Team->current_term_start_date = strtotime('2015/1/1');
        $this->Goal->Team->current_term_end_date = strtotime('2015/12/1');

        $goal_data = [
            'user_id'    => 1,
            'team_id'    => 1,
            'purpose_id' => 1,
            'start_date' => strtotime('2016/1/1'),
            'end_date'   => strtotime('2016/3/1'),
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();

        $res = $this->Goal->isPresentTermGoal($goal_id);
        $this->assertFalse($res);

    }

    function testGetAllUserGoalProgress()
    {
        $this->Goal->current_team_id = 1;
        $goals = $this->Goal->getGoalAndKr(1, 1);
        $goals['KeyResult'][0]['progress'] = 100;
        $this->Goal->KeyResult->save($goals['KeyResult'][0]);

        $this->Goal->getAllUserGoalProgress(1, 1);
    }

    function testSetFollowGoalApprovalFlagNo1()
    {
        $team_id = 100;
        $user_id = 200;
        $goal_id = 300;
        $goal_list[] = [
            'Goal'         => ['id' => $goal_id, 'team_id' => $team_id, 'user_id' => $user_id],
            'Collaborator' => ['user_id' => $user_id, 'goal_id' => $goal_id, 'valued_flg' => 2],
        ];
        $res = $this->Goal->setFollowGoalApprovalFlag($goal_list);
        $this->assertArrayHasKey('owner_approval_flag', $res[0]['Goal']);
    }

    function testSetFollowGoalApprovalFlagNo2()
    {
        $team_id = 100;
        $user_id = 200;
        $goal_id = 300;
        $goal_list[] = ['Goal' => ['id' => $goal_id, 'team_id' => $team_id, 'user_id' => $user_id]];
        $res = $this->Goal->setFollowGoalApprovalFlag($goal_list);
        $this->assertArrayNotHasKey('owner_approval_flag', $res[0]['Goal']);
    }
}
