<?php App::uses('GoalousTestCase', 'Test');
App::uses('Goal', 'Model');

/**
 * Goal Test Case
 *
 * @property Goal $Goal
 */
class GoalTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.action_result',
        'app.evaluation',
        'app.evaluate_term',
        'app.post_share_circle',
        'app.circle',
        'app.post',
        'app.goal',
        'app.goal_label',
        'app.key_result',
        'app.goal_member',
        'app.follower',
        'app.user',
        'app.team',
        'app.team_member',
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
            'user_id'          => 1,
            'team_id'          => 1,
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'goal_category_id' => 1,
            'name'             => 'test',
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $key_results = [
            'goal_id'      => $goal_id,
            'team_id'      => 1,
            'user_id'      => 1,
            'start_date'   => $this->start_date,
            'end_date'     => $this->end_date,
            'value_unit'   => 1,
            'start_value'  => 0,
            'target_value' => 100,
            'name'         => 'test',
        ];
        $this->Goal->KeyResult->save($key_results);
        $this->Goal->getMyGoals();
    }

    function testGetMyGoalsWithNoGoalPurpose()
    {
        $this->setDefault();
        $goal_data = [
            'user_id'          => 1,
            'team_id'          => 1,
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'goal_category_id' => 1,
            'name'             => 'test',
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $key_results = [
            'goal_id'      => $goal_id,
            'team_id'      => 1,
            'user_id'      => 1,
            'start_date'   => $this->start_date,
            'end_date'     => $this->end_date,
            'value_unit'   => 1,
            'start_value'  => 0,
            'target_value' => 100,
            'name'         => 'test',
        ];
        $this->Goal->KeyResult->save($key_results);
        $this->Goal->getMyGoals();
    }

    function testGetAllGoals()
    {
        $this->setDefault();
        $goal_data = [
            'user_id'          => 1,
            'team_id'          => 1,
            'purpose_id'       => 1,
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'goal_category_id' => 1,
            'name'             => 'test',
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $key_results = [
            'goal_id'      => $goal_id,
            'team_id'      => 1,
            'user_id'      => 1,
            'special_flg'  => true,
            'start_date'   => $this->start_date,
            'end_date'     => $this->end_date,
            'completed'    => 1,
            'value_unit'   => 1,
            'start_value'  => 0,
            'target_value' => 100,
            'name'         => 'test',
        ];
        $this->Goal->KeyResult->create();
        $this->Goal->KeyResult->save($key_results);
        $params = [
            'named' => [
                'page' => 1
            ]
        ];
        $res = $this->Goal->getAllGoals(20, null, $params, true);
        $this->assertTrue(!empty($res));
    }

    function testGetByGoalId()
    {
        $this->setDefault();
        $goal_data = [
            'user_id'          => 1,
            'team_id'          => 1,
            'purpose_id'       => 1,
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'goal_category_id' => 1,
            'name'             => 'test',
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $res = $this->Goal->getByGoalId($goal_id, null, 1, 'all', null, null, 2);
        $this->assertTrue(!empty($res));
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
        $res = $this->Goal->add([]);
        $this->assertFalse($res);
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
                'end_date'         => $this->end_date_format,
                'term_type'        => 'current',
            ]
        ];
        $res = $this->Goal->add($data);
        $this->assertTrue($res);
    }

    function testAddNewSuccessWithImgUrl()
    {
        $this->setDefault();
        $data = [
            'Goal' => [
                'purpose_id'       => 1,
                'goal_category_id' => 1,
                'name'             => 'test',
                'end_date'         => $this->end_date_format,
                'img_url'          => 'https://placeholdit.imgix.net/~text?txtsize=14&txt=test&w=1&h=1',
                'term_type'        => 'current',
            ]
        ];
        $res = $this->Goal->add($data);
        $this->assertTrue($res);
    }

    function testAddNewSuccessUnitValue()
    {
        $this->setDefault();
        $data = [
            'Goal' => [
                'purpose_id'       => 1,
                'goal_category_id' => 1,
                'name'             => 'test',
                'end_date'         => $this->end_date_format,
                'term_type'        => 'current',
            ]
        ];
        $res = $this->Goal->add($data);
        $this->assertTrue($res);
    }

    function testIsPermittedAdminNotExists()
    {
        $this->setDefault();
        try {
            $this->Goal->isPermittedAdmin(99999);
        } catch (RuntimeException$e) {

        }
        $this->assertTrue(isset($e));
    }

    function testIsPermittedAdminNotOwner()
    {
        $this->setDefault();
        $this->Goal->save(
            [
                'user_id'          => 999,
                'team_id'          => 1,
                'name'             => 'test',
                'goal_category_id' => 1,
            ]
        );

        try {
            $this->Goal->isPermittedAdmin($this->Goal->getLastInsertID());
        } catch (RuntimeException$e) {

        }
        $this->assertTrue(isset($e));
    }

    function testIsPermittedAdminTrue()
    {
        $this->setDefault();
        $this->Goal->save(
            [
                'user_id'          => 1,
                'team_id'          => 1,
                'name'             => 'test',
                'goal_category_id' => 1,
            ]
        );

        $res = $this->Goal->isPermittedAdmin($this->Goal->getLastInsertID());
        $this->assertTrue($res);
    }

    function testGetMyCreateGoalsList()
    {
        $this->setDefault();
        $res = $this->Goal->getMyCreateGoalsList(1);
        $this->assertNotEmpty($res);
    }

    function testGetMyCollaboGoals()
    {
        $this->setDefault();
        $this->Goal->save(
            [
                'user_id'          => 1,
                'team_id'          => 1,
                'start_date'       => $this->Goal->Team->EvaluateTerm->getCurrentTermData()['start_date'],
                'end_date'         => $this->Goal->Team->EvaluateTerm->getCurrentTermData()['start_date'],
                'name'             => 'test',
                'goal_category_id' => 1,
            ]
        );
        $this->Goal->GoalMember->save(
            [
                'user_id'          => 1,
                'team_id'          => 1,
                'goal_id'          => $this->Goal->getLastInsertID(),
                'name'             => 'test',
                'goal_category_id' => 1,
            ]
        );

        $res = $this->Goal->getMyCollaboGoals();
        $this->assertNotEmpty($res);

        $res = $this->Goal->getMyCollaboGoals(null, 1, 'count');
        $this->assertEquals(1, $res);
    }

    function testGetGoalsWithAction()
    {
        $this->setDefault();
        $this->Goal->save(
            [
                'user_id'          => 1,
                'team_id'          => 1,
                'start_date'       => $this->Goal->Team->EvaluateTerm->getCurrentTermData()['start_date'],
                'end_date'         => $this->Goal->Team->EvaluateTerm->getCurrentTermData()['start_date'],
                'name'             => 'test',
                'goal_category_id' => 1,
            ]
        );
        $this->Goal->GoalMember->save(
            [
                'user_id' => 1,
                'team_id' => 1,
                'goal_id' => $this->Goal->getLastInsertID(),
                'name'    => 'test'
            ]
        );

        $res = $this->Goal->getGoalsWithAction(1);
        $this->assertNotEmpty($res);
    }

    function testGetMyFollowedGoals()
    {
        $this->setDefault();
        $this->Goal->create();
        $this->Goal->save(
            [
                'user_id'          => 2,
                'team_id'          => 1,
                'start_date'       => $this->Goal->Team->EvaluateTerm->getCurrentTermData()['start_date'],
                'end_date'         => $this->Goal->Team->EvaluateTerm->getCurrentTermData()['start_date'],
                'name'             => 'test',
                'goal_category_id' => 1,
            ]
        );
        $goal_1 = $this->Goal->getLastInsertID();
        $this->Goal->create();
        $this->Goal->save(
            [
                'user_id'          => 2,
                'team_id'          => 1,
                'start_date'       => $this->Goal->Team->EvaluateTerm->getCurrentTermData()['start_date'],
                'end_date'         => $this->Goal->Team->EvaluateTerm->getCurrentTermData()['start_date'],
                'name'             => 'test1',
                'goal_category_id' => 1,
            ]
        );
        $goal_2 = $this->Goal->getLastInsertID();

        $this->Goal->GoalMember->create();
        $this->Goal->GoalMember->save(
            [
                'user_id' => 1,
                'team_id' => 1,
                'goal_id' => $goal_1,
                'name'    => 'test'
            ]
        );
        $this->Goal->Follower->create();
        $this->Goal->Follower->save(
            [
                'user_id' => 1,
                'team_id' => 1,
                'goal_id' => $goal_2,
            ]
        );

        $res = $this->Goal->getMyFollowedGoals();
        $this->assertNotEmpty($res);
        $res = $this->Goal->getMyFollowedGoals(null, 1, 'count');
        $this->assertEquals(1, $res);
    }

    function testGetGoal()
    {
        $this->setDefault();
        $res = $this->Goal->getGoal(1);
        $this->assertNotEmpty($res);
    }

    function _getNewGoal()
    {
        $goal = [
            'user_id'          => 1,
            'team_id'          => 1,
            'name'             => 'test',
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'goal_category_id' => 1,
        ];
        $this->Goal->create();
        $this->Goal->save($goal);
        $goal_id = $this->Goal->getLastInsertID();
        $kr = [
            'user_id'      => 1,
            'team_id'      => 1,
            'goal_id'      => $goal_id,
            'name'         => 'test',
            'start_date'   => $this->start_date,
            'end_date'     => $this->end_date,
            'value_unit'   => 1,
            'start_value'  => 0,
            'target_value' => 100,
        ];
        $this->Goal->KeyResult->create();
        $this->Goal->KeyResult->save($kr);
        $goalMember = [
            'user_id' => 1,
            'team_id' => 1,
            'goal_id' => $goal_id,
        ];
        $this->Goal->GoalMember->create();
        $this->Goal->GoalMember->save($goalMember);
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
    var $start_date_format;
    var $end_date_format;

    function setDefault()
    {

        $this->Goal->my_uid = 1;
        $this->Goal->current_team_id = 1;
        $this->Goal->Team->my_uid = 1;
        $this->Goal->Team->current_team_id = 1;
        $this->Goal->KeyResult->my_uid = 1;
        $this->Goal->KeyResult->current_team_id = 1;
        $this->Goal->GoalMember->my_uid = 1;
        $this->Goal->GoalMember->current_team_id = 1;
        $this->Goal->Follower->my_uid = 1;
        $this->Goal->Follower->current_team_id = 1;
        $this->Goal->Post->my_uid = 1;
        $this->Goal->Post->current_team_id = 1;
        $this->Goal->Evaluation->current_team_id = 1;
        $this->Goal->Evaluation->my_uid = 1;
        $this->Goal->Team->EvaluateTerm->current_team_id = 1;
        $this->Goal->Team->EvaluateTerm->my_uid = 1;

        $this->Goal->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->Goal->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->Goal->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $this->current_date = REQUEST_TIMESTAMP;
        $this->start_date = $this->Goal->Team->EvaluateTerm->getCurrentTermData()['start_date'];
        $this->end_date = $this->Goal->Team->EvaluateTerm->getCurrentTermData()['end_date'];
        $timezone = $this->Goal->Team->EvaluateTerm->getCurrentTermData()['timezone'];
        $this->start_date_format = date('Y-m-d', $this->start_date + $timezone * HOUR);
        $this->end_date_format = date('Y-m-d', $this->end_date + $timezone * HOUR);

    }

    function testGetGoalIdFromUserId()
    {
        $this->setDefault();
        $user_id = 1;
        $team_id = 1;
        $goal_params = [
            'user_id'          => $user_id,
            'team_id'          => $team_id,
            'name'             => 'test',
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'goal_category_id' => 1,
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
        $current = $this->Goal->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->Goal->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $this->Goal->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);

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
        $term = $this->Goal->Team->EvaluateTerm->getTermData(EvaluateTerm::TYPE_PREVIOUS);
        $goal_data = [
            'user_id'          => 1,
            'team_id'          => 1,
            'purpose_id'       => 1,
            'start_date'       => $term['start_date'] + 1,
            'end_date'         => $term['end_date'] - 1,
            'goal_category_id' => 1,
            'name'             => 'test',
        ];
        $this->Goal->create();
        $this->Goal->save($goal_data);
        $goal_data = [
            'user_id'          => 2,
            'team_id'          => 1,
            'purpose_id'       => 1,
            'start_date'       => $term['start_date'] + 1,
            'end_date'         => $term['end_date'] - 1,
            'goal_category_id' => 1,
            'name'             => 'test',
        ];
        $this->Goal->create();
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();
        $goalMember = [
            'user_id' => 1,
            'team_id' => 1,
            'goal_id' => $goal_id,
        ];
        $key_results = [
            'goal_id'      => $goal_id,
            'team_id'      => 1,
            'user_id'      => 1,
            'start_date'   => $this->start_date + 1,
            'end_date'     => $this->end_date - 1,
            'value_unit'   => 1,
            'start_value'  => 0,
            'target_value' => 100,
            'name'         => 'test',
        ];
        $this->Goal->KeyResult->save($key_results);
        $this->Goal->KeyResult->save($key_results);
        $this->Goal->GoalMember->create();
        $this->Goal->GoalMember->save($goalMember);
        $res_1 = $this->Goal->getMyPreviousGoals(null, 1, 'all', 2);
        $res_2 = $this->Goal->getMyPreviousGoals(null, 1, 'count', 2);
        $this->assertNotEmpty($res_1);
        $this->assertNotEquals(0, $res_2);
    }

    function testIsPresentTermGoalPatternTrue()
    {
        $this->setDefault();
        $goal_data = [
            'user_id'          => 1,
            'team_id'          => 1,
            'purpose_id'       => 1,
            'start_date'       => REQUEST_TIMESTAMP,
            'end_date'         => $this->Goal->Team->EvaluateTerm->getCurrentTermData()['end_date'],
            'goal_category_id' => 1,
            'name'             => 'test',
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();

        $res = $this->Goal->isPresentTermGoal($goal_id);
        $this->assertTrue($res);
    }

    function testIsPresentTermGoalPatternFalse()
    {
        $this->setDefault();

        $goal_data = [
            'user_id'          => 1,
            'team_id'          => 1,
            'purpose_id'       => 1,
            'start_date'       => $this->Goal->Team->EvaluateTerm->getPreviousTermData()['start_date'],
            'end_date'         => $this->Goal->Team->EvaluateTerm->getPreviousTermData()['end_date'],
            'goal_category_id' => 1,
            'name'             => 'test',
        ];
        $this->Goal->save($goal_data);
        $goal_id = $this->Goal->getLastInsertID();

        $res = $this->Goal->isPresentTermGoal($goal_id);
        $this->assertFalse($res);

    }

    function testIsPresentTermGoalNullFalse()
    {
        $this->assertFalse($this->Goal->isPresentTermGoal(null));
    }

    function testGetAllUserGoal()
    {
        $this->setDefault();
        $this->Goal->User->TeamMember->current_team_id = 1;
        $this->Goal->User->TeamMember->my_uid = 1;

        $users = $this->Goal->getAllUserGoal();
        $active_user_count =
            $this->Goal->User->TeamMember->countActiveMembersByTeamId($this->Goal->User->TeamMember->current_team_id);
        $this->assertEquals($active_user_count, count($users));

        // ゴールの期限が範囲内に収まっているかチェック
        $users = $this->Goal->getAllUserGoal(10000, 19999);
        foreach ($users as $user) {
            foreach ($user['GoalMember'] as $goalMember) {
                if ($goalMember['Goal']) {
                    $this->assertGreaterThanOrEqual(10000, $goalMember['Goal']['start_date']);
                    $this->assertLessThanOrEqual(19999, $goalMember['Goal']['end_date']);
                }
            }
        }
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
            'Goal'       => ['id' => $goal_id, 'team_id' => $team_id, 'user_id' => $user_id],
            'GoalMember' => ['user_id' => $user_id, 'goal_id' => $goal_id, 'approval_status' => 2],
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

    function testGetGoalNameList()
    {
        $this->setDefault();
        $res = $this->Goal->getGoalNameListByGoalIds(1, true);
        $this->assertNotEmpty($res);
    }

    function testGetGoalNameListSeparate()
    {
        $this->setDefault();
        $res = $this->Goal->getGoalNameListByGoalIds(1, true, true);
        $this->assertNotEmpty($res);
    }

    function testGetGoalsByKeyword()
    {
        $this->setDefault();

        // 自分のゴール
        $goals = $this->Goal->getGoalsByKeyword('ゴール');
        $this->assertNotEmpty($goals);

        // 他人のゴール
        $goals = $this->Goal->getGoalsByKeyword('その他');
        $this->assertNotEmpty($goals);
    }

    function testGetGoalsSelect2()
    {
        $this->setDefault();

        // 自分のゴール
        $goals = $this->Goal->getGoalsSelect2('ゴール');
        $this->assertArrayHasKey('results', $goals);
        $this->assertNotEmpty($goals['results']);
        $this->assertArrayHasKey('id', $goals['results'][0]);
        $this->assertArrayHasKey('text', $goals['results'][0]);
        $this->assertArrayHasKey('image', $goals['results'][0]);

        // 他人のゴール
        $goals = $this->Goal->getGoalsSelect2('その他');
        $this->assertArrayHasKey('results', $goals);
        $this->assertNotEmpty($goals['results']);
        $this->assertArrayHasKey('id', $goals['results'][0]);
        $this->assertArrayHasKey('text', $goals['results'][0]);
        $this->assertArrayHasKey('image', $goals['results'][0]);
    }

    function testGetGoalsWithUser()
    {
        $this->setDefault();
        $goals = $this->Goal->getGoalsWithUser(1);
        $this->assertEquals(1, $goals[0]['Goal']['id']);
        $this->assertEquals(1, $goals[0]['User']['id']);

        $goals = $this->Goal->getGoalsWithUser([1, 7]);
        $this->assertEquals(1, $goals[0]['Goal']['id']);
        $this->assertEquals(1, $goals[0]['User']['id']);
        $this->assertEquals(7, $goals[1]['Goal']['id']);
        $this->assertEquals(2, $goals[1]['User']['id']);
    }

    function testSetIsCurrentTerm()
    {
        $this->setDefault();
        $goals = [
            ['Goal' => ['end_date' => $this->Goal->Team->EvaluateTerm->getPreviousTermData()['end_date']],],
            ['Goal' => ['end_date' => $this->Goal->Team->EvaluateTerm->getCurrentTermData()['end_date']],],
            ['Goal' => ['end_date' => $this->Goal->Team->EvaluateTerm->getNextTermData()['end_date']],],
        ];
        $actual = $this->Goal->setIsCurrentTerm($goals);
        foreach ($actual as $k => $v) {
            unset($actual[$k]['Goal']['end_date']);
        }
        $expected = [
            (int)0 => [
                'Goal' => [
                    'is_current_term' => false
                ]
            ],
            (int)1 => [
                'Goal' => [
                    'is_current_term' => true
                ]
            ],
            (int)2 => [
                'Goal' => [
                    'is_current_term' => false
                ]
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    function testGetGoalTermData()
    {
        $this->setDefault();

        $term = $this->Goal->getGoalTermData(8);
        $this->assertEquals(2, $term['id']);
        $term = $this->Goal->getGoalTermData(999999);
        $this->assertFalse($term);
    }

    function testGetAllMyGoalNameList()
    {
        $this->setDefault();
        $term = $this->Goal->Team->EvaluateTerm->getCurrentTermData();
        $this->Goal->create();
        $this->Goal->save(
            [
                'user_id'          => $this->Goal->my_uid,
                'team_id'          => $this->Goal->current_team_id,
                'start_date'       => $term['start_date'],
                'end_date'         => $term['end_date'],
                'name'             => 'test',
                'goal_category_id' => 1,
            ]
        );
        $this->Goal->GoalMember->create();
        $this->Goal->GoalMember->save(
            [
                'goal_id' => $this->Goal->getLastInsertID(),
                'user_id' => $this->Goal->my_uid,
                'team_id' => $this->Goal->current_team_id,
            ]
        );
        $res = $this->Goal->getAllMyGoalNameList($term['start_date'], $term['end_date']);
        $this->assertNotEmpty($res);
    }

    function testCountGoalRes()
    {
        $this->setDefault();
        $res = $this->Goal->countGoalRes([]);
        $this->assertEquals(0, $res);
    }

    function testIncomplete()
    {
        $this->setDefault();
        $res = $this->Goal->incomplete(1);
        $this->assertTrue($res);
    }

    function testGetCollaboModel()
    {
        $this->setDefault();
        $res = $this->Goal->getCollaboModalItem(1);
        $this->assertNotEmpty($res);
    }

    function testIsCreatedsForSetupBy()
    {
        $this->setDefault();

        // In case that goal is created in current term or previous term
        $this->Goal->save([
            'user_id'          => $this->Goal->my_uid,
            'team_id'          => $this->Goal->current_team_id,
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'goal_category_id' => 1,
            'name'             => 'test',
        ]);
        $res = $this->Goal->isCreatedForSetupBy($this->Goal->my_uid);
        $this->assertTrue($res);

        // In case that goal is not created in current term or previous term
        $this->Goal->deleteAll([
            'Goal.user_id'       => $this->Goal->my_uid,
            'Goal.team_id'       => $this->Goal->current_team_id,
            'Goal.start_date >=' => $this->Goal->Team->EvaluateTerm->getPreviousTermData()['start_date'],
            'Goal.end_date <='   => $this->end_date
        ]);
        $res = $this->Goal->isCreatedForSetupBy($this->Goal->my_uid);
        $this->assertFalse($res);
    }

    function testIsPostedActionForSetupBy()
    {
        $this->setDefault();

        // In case that action is posted in current term or previous term
        $this->Goal->ActionResult->save([
            'user_id' => $this->Goal->my_uid,
            'team_id' => $this->Goal->current_team_id,
            'created' => $this->start_date,
        ]);
        $res = $this->Goal->ActionResult->isPostedActionForSetupBy($this->Goal->my_uid);
        $this->assertTrue($res);

        // In case that action is not posted in current term or previous term
        $this->Goal->ActionResult->deleteAll([
            'ActionResult.user_id'    => $this->Goal->my_uid,
            'ActionResult.created >=' => $this->Goal->Team->EvaluateTerm->getPreviousTermData()['start_date'],
            'ActionResult.created <=' => $this->end_date
        ]);
        $res = $this->Goal->ActionResult->isPostedActionForSetupBy($this->Goal->my_uid);
        $this->assertFalse($res);
    }

    function testGetGoalsForSetupBy()
    {
        $this->setDefault();
        $goal_data = [
            'user_id'          => 1,
            'team_id'          => 1,
            'start_date'       => $this->start_date,
            'end_date'         => $this->end_date,
            'goal_category_id' => 1,
            'name'             => 'test',
        ];
        $this->Goal->save($goal_data);
        $goals = $this->Goal->getGoalsForSetupBy(1);
        $this->assertNotEmpty($goals);
    }

}
