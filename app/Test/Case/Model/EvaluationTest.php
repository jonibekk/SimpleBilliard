<?php App::uses('GoalousTestCase', 'Test');
App::uses('Evaluation', 'Model');

/**
 * Evaluation Test Case
 *
 * @property Evaluation $Evaluation
 */
class EvaluationTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(

        'app.evaluation',
        'app.team',
        'app.user',
        'app.email',
        'app.goal',
        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.collaborator',
        'app.team_member',
        'app.job_category',
        'app.member_type',
        'app.local_name',
        'app.evaluator',
        'app.evaluate_term',
        'app.evaluate_score',
        'app.evaluation_setting'
    );
    private $current_date;
    private $start_date;
    private $end_date;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Evaluation = ClassRegistry::init('Evaluation');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Evaluation);

        parent::tearDown();
    }

    function testCheckAvailViewEvaluateListNoTeamMember()
    {
        $this->Evaluation->Team->TeamMember->deleteAll(['TeamMember.team_id' => 1]);
        try {
            $this->Evaluation->checkAvailViewEvaluationList();
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testCheckAvailParameterInEvalFormParameterIsNull()
    {
        $this->_setDefault();
        $termId = null;
        $evaluateeId = null;
        try {
            $this->Evaluation->checkAvailParameterInEvalForm($termId, $evaluateeId);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testCheckAvailParameterInEvalFormTermIdIsIncorrect()
    {
        $this->_setDefault();
        $termId = 1;
        $evaluateeId = 1000;
        try {
            $this->Evaluation->checkAvailParameterInEvalForm($termId, $evaluateeId);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testCheckAvailParameterInEvalFormEvaluateeIdIsIncorrect()
    {
        $this->_setDefault();
        $termId = 1000;
        $evaluateeId = 1;
        try {
            $this->Evaluation->checkAvailParameterInEvalForm($termId, $evaluateeId);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testCheckAvailParameterInEvalFormStatusCannotGet()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $termId = $this->Evaluation->Team->EvaluateTerm->getLastInsertID();
        $this->Evaluation->deleteAll(['Evaluation.id >' => 0]);
        $evaluateeId = 1;
        try {
            $this->Evaluation->checkAvailParameterInEvalForm($termId, $evaluateeId);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testCheckAvailParameterInEvalFormNotStatus()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $termId = $this->Evaluation->Team->EvaluateTerm->getLastInsertID();
        $this->Evaluation->Team->EvaluateTerm->changeToInProgress($termId);
        $this->Evaluation->deleteAll(['Evaluation.id >' => 0]);
        $evaluateeId = 1;
        try {
            $this->Evaluation->checkAvailParameterInEvalForm($termId, $evaluateeId);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testCheckAvailParameterInEvalFormSuccess()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $termId = $this->Evaluation->Team->EvaluateTerm->getLastInsertID();
        $this->Evaluation->Team->EvaluateTerm->changeToInProgress($termId);
        $this->Evaluation->deleteAll(['Evaluation.id >' => 0]);
        $this->Evaluation->save(
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => $termId,
            ]
        );
        $evaluateeId = 1;
        $res = $this->Evaluation->checkAvailParameterInEvalForm($termId, $evaluateeId);
        $this->assertTrue($res);
    }

    function testAddDrafts()
    {
        $this->_setDefault();

        $draftData = [
            [
                'Evaluation' => [
                    'id'                => 1,
                    'team_id'           => 1,
                    'comment'           => 'あいうえお',
                    'evaluate_score_id' => 1,
                    'evaluatee_user_id' => 1,
                    'evaluate_term_id'  => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 2,
                    'team_id'           => 1,
                    'comment'           => 'かきくけこ',
                    'evaluate_score_id' => 1,
                    'evaluatee_user_id' => 1,
                    'evaluate_term_id'  => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 3,
                    'team_id'           => 1,
                    'comment'           => 'さしすせそ',
                    'evaluate_score_id' => 1,
                    'evaluatee_user_id' => 1,
                    'evaluate_term_id'  => 1,
                ],
            ],
        ];
        $res = $this->Evaluation->add($draftData, Evaluation::TYPE_STATUS_DRAFT);
        $this->assertNotEmpty($res, "[正常]下書き保存");
        $res = $this->Evaluation->find('all',
            [
                'conditions' => [
                    'evaluatee_user_id' => 1,
                    'evaluate_term_id'  => 1,
                    'status'            => Evaluation::TYPE_STATUS_DRAFT
                ]
            ]
        );
        $this->assertEquals(count($res), count($draftData));
    }

    function testAddRegisters()
    {
        $this->_setDefault();

        $registerData = [
            [
                'Evaluation' => [
                    'id'                => 1,
                    'team_id'           => 1,
                    'comment'           => 'あいうえお',
                    'evaluate_score_id' => 1,
                    'evaluatee_user_id' => 1,
                    'evaluate_term_id'  => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 2,
                    'team_id'           => 1,
                    'comment'           => 'かきくけこ',
                    'evaluate_score_id' => 1,
                    'evaluatee_user_id' => 1,
                    'evaluate_term_id'  => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 3,
                    'team_id'           => 1,
                    'comment'           => 'さしすせそ',
                    'evaluate_score_id' => 1,
                    'evaluatee_user_id' => 1,
                    'evaluate_term_id'  => 1,
                ],
            ],
        ];
        $res = $this->Evaluation->add($registerData, Evaluation::TYPE_STATUS_DONE);
        $this->assertNotEmpty($res, "[正常]評価登録");
        $res = $this->Evaluation->find(
            'all',
            [
                'conditions' => [
                    'evaluatee_user_id' => 1,
                    'evaluate_term_id'  => 1,
                    'status'            => Evaluation::TYPE_STATUS_DONE
                ]
            ]
        );
        $this->assertEquals(count($res), count($registerData));
    }

    function testAddRegisterAsEvaluatorHadNextEvaluator()
    {
        $this->_setDefault();
        $this->_saveEvaluations();
        $registerData = [
            [
                'Evaluation' => [
                    'id'                => 2,
                    'team_id'           => 1,
                    'comment'           => 'あいうえお',
                    'evaluate_score_id' => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 5,
                    'team_id'           => 1,
                    'comment'           => 'かきくけこ',
                    'evaluate_score_id' => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 8,
                    'team_id'           => 1,
                    'comment'           => 'さしすせそ',
                    'evaluate_score_id' => 1,
                ],
            ],
        ];
        $this->Evaluation->add($registerData, Evaluation::TYPE_STATUS_DONE);
    }

    function testAddRegisterAsLastEvaluatorInEvaluator()
    {
        $this->_setDefault();
        $this->Evaluation->deleteAll(['Evaluation.id >' => 0]);
        $this->_saveEvaluations();
        $registerData = [
            [
                'Evaluation' => [
                    'id'                => 3,
                    'team_id'           => 1,
                    'comment'           => 'あいうえお',
                    'evaluate_score_id' => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 6,
                    'team_id'           => 1,
                    'comment'           => 'かきくけこ',
                    'evaluate_score_id' => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 9,
                    'team_id'           => 1,
                    'comment'           => 'さしすせそ',
                    'evaluate_score_id' => 1,
                ],
            ],
        ];
        $this->Evaluation->add($registerData, Evaluation::TYPE_STATUS_DONE);
    }

    function testAddRegistersValidationError()
    {
        $this->_setDefault();

        $registerData = [
            [
                'Evaluation' => [
                    'id'                => 1,
                    'team_id'           => 1,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 2,
                    'team_id'           => 1,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 3,
                    'team_id'           => 1,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                ],
            ],
        ];
        $this->setExpectedException('RuntimeException');
        $this->Evaluation->add($registerData, "register");
    }

    function testCheckAvailViewEvaluateListTrue()
    {
        $this->Evaluation->Team->TeamMember->deleteAll(['TeamMember.team_id' => 1]);
        $data = [
            'team_id'               => 1,
            'user_id'               => 1,
            'evaluation_enable_flg' => 1,
        ];
        $this->Evaluation->Team->TeamMember->save($data);
        $this->Evaluation->Team->TeamMember->current_team_id = 1;
        $this->Evaluation->Team->TeamMember->my_uid = 1;
        $res = $this->Evaluation->checkAvailViewEvaluationList();
        $this->assertTrue($res);
    }

    function testGetStatus()
    {
        $this->_setDefault();
        $this->Evaluation->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $term_id = $this->Evaluation->EvaluateTerm->getLastInsertID();
        $data = [
            'team_id'           => 1,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 1,
            'status'            => 0
        ];
        $this->Evaluation->save($data);
        $eval_id = $this->Evaluation->getLastInsertID();
        $res = $this->Evaluation->getStatus($term_id, 1, 1);
        $this->assertEquals(0, $res);

        $this->Evaluation->id = $eval_id;
        $this->Evaluation->saveField('evaluate_type', Evaluation::TYPE_FINAL_EVALUATOR);
        $res = $this->Evaluation->getStatus($term_id, 1, 1);
        $this->assertNull($res);
    }

    function testGetEvaluateType()
    {
        $this->_setDefault();
        $this->Evaluation->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $term_id = $this->Evaluation->EvaluateTerm->getLastInsertID();
        $data = [
            'team_id'           => 1,
            'evaluate_term_id'  => $term_id,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 1,
            'evaluate_type'     => 1,
            'status'            => 0
        ];
        $this->Evaluation->save($data);
        $res = $this->Evaluation->getEvaluateType($term_id, 1);
        $this->assertEquals(1, $res);
    }

    function testGetMyEvaluations()
    {
        $this->Evaluation->deleteAll(['Evaluation.team_id' => 1]);
        $data = [
            'team_id'           => 1,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 2,
            'status'            => 0
        ];
        $this->Evaluation->save($data);
        $this->Evaluation->current_team_id = 1;
        $this->Evaluation->my_uid = 1;
        $actual = $this->Evaluation->getMyEvaluation();
        $expect = [
            (int)0 => [
                'Evaluation' => [
                    'id'                => '2',
                    'team_id'           => '1',
                    'evaluatee_user_id' => '1',
                    'evaluator_user_id' => '2',
                    'evaluate_term_id'  => null,
                    'evaluate_type'     => '0',
                    'goal_id'           => null,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'index_num'         => '0',
                    'status'            => '0',
                    'del_flg'           => false,
                    'deleted'           => null,
                    'created'           => '1426206160',
                    'modified'          => '1426206160'
                ]
            ]
        ];
        unset($actual[0]['Evaluation']['created']);
        unset($actual[0]['Evaluation']['modified']);
        $this->assertEquals(count($expect), count($actual));
    }

    function testGetEvaluations()
    {
        $this->_setDefault();
        $this->Evaluation->deleteAll(['Evaluation.id >' => 0]);
        $evaluateTermId = 1;
        $evaluateeId = 1;
        $goalNum = 4;

        $records = [
            [
                'Evaluation' => [
                    'id'                => 1,
                    'team_id'           => 1,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evaluateTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'index_num'         => 0,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 2,
                    'team_id'           => 1,
                    'evaluatee_user_id' => 2,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evaluateTermId,
                    'goal_id'           => 1,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'index_num'         => 0,
                    'status'            => 0
                ],

            ],
            [
                'Evaluation' => [
                    'id'                => 3,
                    'team_id'           => 1,
                    'evaluatee_user_id' => 2,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evaluateTermId,
                    'goal_id'           => 2,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'index_num'         => 1,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 4,
                    'team_id'           => 1,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evaluateTermId,
                    'goal_id'           => 3,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'index_num'         => 1,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 5,
                    'team_id'           => 1,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evaluateTermId,
                    'goal_id'           => 4,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'index_num'         => 2,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 6,
                    'team_id'           => 1,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evaluateTermId,
                    'goal_id'           => 5,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'index_num'         => 3,
                    'status'            => 0
                ],
            ],
        ];
        $this->Evaluation->saveAll($records);
        $res = $this->Evaluation->getEvaluations($evaluateTermId, $evaluateeId);
        $this->assertNotEmpty($res, "[正常]評価登録");

        // ゴールで配列をグループ化してるため、ゴール数でアサーション
        $this->assertEquals(count($res), $goalNum);
    }

    /**
     * setAllowEmptyToComment method
     *
     * @return void
     */
    function testSetAllowEmptyToCommentCaseOfEmpty()
    {
        $this->_setDefault();
        $this->Evaluation->validate['comment'] = [
            'isString'  => [
                'rule'       => ['isString'],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
        ];
        $required = [
            'isString'  => [
                'rule'       => ['isString'],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
        ];
        $this->Evaluation->setAllowEmptyToComment();
        $this->assertEquals($this->Evaluation->validate['comment'], $required);

    }

    function testSetAllowEmptyToCommentCaseOfNotEmpty()
    {
        $this->_setDefault();
        $this->Evaluation->validate['comment'] = [
            'isString'  => [
                'rule' => ['isString'],
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
            'notBlank'  => ['rule' => 'notBlank']
        ];
        $required = [
            'isString'  => [
                'rule'       => ['isString'],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
        ];
        $this->Evaluation->setAllowEmptyToComment();
        $this->assertEquals($this->Evaluation->validate['comment'], $required);
    }

    /**
     * setNotAllowEmptyToComment method
     *
     * @return void
     */
    public function testSetNotAllowEmptyToCommentCaseOfEmpty()
    {
        $this->_setDefault();
        $this->Evaluation->validate['comment'] = [
            'isString'  => [
                'rule'       => ['isString'],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
        ];
        $required = [
            'isString'  => [
                'rule' => ['isString'],
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
            'notBlank'  => ['rule' => 'notBlank']
        ];
        $this->Evaluation->setNotAllowEmptyToComment();
        $this->assertEquals($this->Evaluation->validate['comment'], $required);

    }

    public function testSetNotAllowEmptyToCommentCaseOfNotEmpty()
    {
        $this->_setDefault();
        $this->Evaluation->validate['comment'] = [
            'isString'  => [
                'rule' => ['isString'],
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
            'notBlank'  => ['rule' => 'notBlank']
        ];
        $required = [
            'isString'  => [
                'rule' => ['isString'],
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
            'notBlank'  => ['rule' => 'notBlank'],
        ];
        $this->Evaluation->setNotAllowEmptyToComment();
        $this->assertEquals($this->Evaluation->validate['comment'], $required);
    }

    /**
     * setAllowEmptyToEvaluateScoreId method
     *
     * @return void
     */
    public function testSetAllowEmptyToEvaluateScoreIdCaseOfEmpty()
    {
        $this->_setDefault();
        $this->Evaluation->validate['evaluate_score_id'] = [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ];
        $required = [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ];
        $this->Evaluation->setAllowEmptyToEvaluateScoreId();
        $this->assertEquals($this->Evaluation->validate['evaluate_score_id'], $required);
    }

    public function testSetAllowEmptyToEvaluateScoreIdCaseOfNotEmpty()
    {
        $this->_setDefault();
        $this->Evaluation->validate['evaluate_score_id'] = [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => ['rule' => 'notBlank'],

        ];
        $required = [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ];
        $this->Evaluation->setAllowEmptyToEvaluateScoreId();
        $this->assertEquals($this->Evaluation->validate['evaluate_score_id'], $required);
    }

    /**
     * setNotAllowEmptyToComment method
     *
     * @return void
     */
    public function testSetNotAllowEmptyToEvaluateScoreIdCaseOfEmpty()
    {
        $this->_setDefault();
        $this->Evaluation->validate['evaluate_score_id'] = [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ];
        $required = [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => ['rule' => 'notBlank'],
        ];
        $this->Evaluation->setNotAllowEmptyToEvaluateScoreId();
        $this->assertEquals($this->Evaluation->validate['evaluate_score_id'], $required);
    }

    public function testSetNotAllowEmptyToEvaluateScoreIdCaseOfNotEmpty()
    {
        $this->_setDefault();
        $this->Evaluation->validate['evaluate_score_id'] = [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => ['rule' => 'notBlank'],
        ];
        $required = [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => ['rule' => 'notBlank'],
        ];
        $this->Evaluation->setNotAllowEmptyToEvaluateScoreId();
        $this->assertEquals($this->Evaluation->validate['evaluate_score_id'], $required);
    }

    function testStartEvaluationNotEnabled()
    {
        $this->Evaluation->current_team_id = 1;
        $this->Evaluation->my_uid = 1;
        $res = $this->Evaluation->startEvaluation();
        $this->assertFalse($res);
    }

    function testStartEvaluationNotExistsCurrentTerm()
    {
        $this->_setDefault();
        $this->Evaluation->EvaluateTerm->deleteAll(['team_id' => 1], false);
        $this->Evaluation->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_CURRENT);
        $this->Evaluation->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_PREVIOUS);
        $this->Evaluation->EvaluateTerm->resetTermProperty(EvaluateTerm::TYPE_NEXT);
        $res = $this->Evaluation->startEvaluation();
        $this->assertTrue($res);
    }

    function testStartEvaluationTeamMembersAreNotExists()
    {
        $this->_setDefault();
        $this->Evaluation->Team->TeamMember->deleteAll(['TeamMember.team_id' => 1], false);
        $res = $this->Evaluation->startEvaluation();
        $this->assertFalse($res);
    }

    function testStartEvaluationAllEnabled()
    {
        $this->_setDefault();
        $res = $this->Evaluation->startEvaluation();
        $this->assertTrue($res);
    }

    function testGetMyEvalStatus()
    {
        $this->_setDefault();
        $eval_term = [
            'team_id'    => 1,
            'start_date' => 1,
            'end_date'   => 1,
        ];
        $this->Evaluation->Team->EvaluateTerm->save($eval_term);
        $term_id = $this->Evaluation->Team->EvaluateTerm->getLastInsertID();
        $eval = [
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => $term_id,
                'evaluate_type'     => 0,
                'index_num'         => 0,
                'my_turn_flg'       => true,
            ],
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 2,
                'evaluate_term_id'  => $term_id,
                'evaluate_type'     => Evaluation::TYPE_EVALUATOR,
                'index_num'         => 1,
            ],
        ];
        $this->Evaluation->saveAll($eval);
        $expected = [
            (int)0 => [
                'name'      => 'You',
                'status'    => '0',
                'this_turn' => true
            ],
            (int)1 => [
                'name'      => 'Evaluator1',
                'status'    => '0',
                'this_turn' => false
            ]
        ];
        $actual = $this->Evaluation->getEvalStatus($term_id, 1);
        $this->assertEquals($expected, $actual['flow']);
    }

    function testGetMyEvalStatusHasNoFlow()
    {
        $this->_setDefault();
        $eval_term = [
            'team_id'    => 1,
            'start_date' => 1,
            'end_date'   => 1,
        ];
        $this->Evaluation->Team->EvaluateTerm->save($eval_term);
        $term_id = $this->Evaluation->Team->EvaluateTerm->getLastInsertID();
        $actual = $this->Evaluation->getEvalStatus($term_id, 1);
        $expected = [];
        $this->assertEquals($actual, $expected);
    }

    function testGetEvaluateeEvalStatusAsEvaluator()
    {
        $this->_setDefault();
        $eval_term = [
            'team_id'    => 1,
            'start_date' => 1,
            'end_date'   => 1,
        ];
        $this->Evaluation->Team->EvaluateTerm->save($eval_term);
        $term_id = $this->Evaluation->Team->EvaluateTerm->getLastInsertID();
        $eval = [
            //自分の番
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 2,
                'evaluate_term_id'  => $term_id,
                'evaluate_type'     => Evaluation::TYPE_ONESELF,
                'index_num'         => 0,
                'my_turn_flg'       => false,
            ],
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => $term_id,
                'evaluate_type'     => Evaluation::TYPE_EVALUATOR,
                'index_num'         => 1,
                'my_turn_flg'       => true,
            ],
            //他人の番
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 3,
                'evaluator_user_id' => 3,
                'evaluate_term_id'  => $term_id,
                'evaluate_type'     => Evaluation::TYPE_ONESELF,
                'index_num'         => 0,
                'my_turn_flg'       => true,
            ],
            [
                'team_id'           => 1,
                'evaluatee_user_id' => 3,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => $term_id,
                'evaluate_type'     => Evaluation::TYPE_EVALUATOR,
                'index_num'         => 1,
                'my_turn_flg'       => false,
            ],
        ];
        $this->Evaluation->saveAll($eval);
        $actual = $this->Evaluation->getEvaluateeEvalStatusAsEvaluator($term_id);
        $this->assertCount(2, $actual[0]['flow']);
        $this->assertCount(2, $actual[1]['flow']);
        $this->assertTrue($actual[0]['status_text']['your_turn']);
        $this->assertFalse($actual[1]['status_text']['your_turn']);
    }

    function testGetAddRecordsOfEvaluatee()
    {
        $this->_setDefault();
        $current_start = $this->Evaluation->Team->EvaluateTerm->getCurrentTermData()['start_date'];
        $current_end = $this->Evaluation->Team->EvaluateTerm->getCurrentTermData()['end_date'];
        $evaluators_save_data = [
            [
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 2,
                'team_id'           => 1,
                'index_num'         => 0,
            ],
            [
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 3,
                'team_id'           => 1,
                'index_num'         => 1,
            ],
        ];
        $this->Evaluation->Team->Evaluator->saveAll($evaluators_save_data);
        $evaluators = $this->Evaluation->Team->Evaluator->getEvaluatorsCombined();

        $collabo = $this->Evaluation->Goal->Collaborator->find('all');
        foreach ($collabo as $k => $v) {
            $collabo[$k]['Collaborator']['approval_status'] = Collaborator::APPROVAL_STATUS_REAPPLICATION;
        }
        $this->Evaluation->Goal->Collaborator->saveAll($collabo);
        $this->Evaluation->Goal->id = 1;
        $this->Evaluation->Goal->saveField('start_date', $current_start);
        $this->Evaluation->Goal->saveField('end_date', $current_end);

        $res = $this->Evaluation->getAddRecordsOfGoalEvaluation(1, 1, $evaluators, 0);
        $this->assertCount(5, $res);
    }

    function testGetMyTurnCountCaseCurrentTermIsFrozen()
    {
        $this->_setDefault();
        $this->Evaluation->Team->current_team_id = 1;
        $this->Evaluation->Team->my_uid = 1;
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $currentTermId = $this->Evaluation->Team->EvaluateTerm->getLastInsertID();
        $this->Evaluation->Team->EvaluateTerm->changeFreezeStatus($currentTermId);
        $this->Evaluation->getMyTurnCount();
    }

    function testGetMyTurnCountCasePreviousTermIsFrozen()
    {
        $this->_setDefault();
        $this->Evaluation->Team->current_team_id = 1;
        $this->Evaluation->Team->my_uid = 1;
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $previousTermId = $this->Evaluation->Team->EvaluateTerm->getLastInsertID();
        $previous = $this->Evaluation->EvaluateTerm->getTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->Evaluation->Team->EvaluateTerm->save([
            'id'         => $previousTermId,
            'start_date' => $previous['start_date'],
            'end_date'   => $previous['end_date']
        ]);
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->Evaluation->Team->EvaluateTerm->changeFreezeStatus($previousTermId);
        $this->Evaluation->getMyTurnCount();
    }

    function testGetCurrentTurnEvaluationId()
    {
        $this->_setDefault();
        $this->_saveEvaluations();
        $res = $this->Evaluation->getCurrentTurnEvaluationId(1, $this->Evaluation->EvaluateTerm->getCurrentTermId());
        $this->assertEquals(2, $res);
    }

    function testGetTermIdByEvaluationId()
    {
        $this->_setDefault();
        $this->Evaluation->deleteAll(['Evaluation.id >' => 0]);
        $this->_saveEvaluations();
        $res = $this->Evaluation->find("first");
        $expectedId = $res['Evaluation']['id'];
        $expectedTermId = $res['Evaluation']['evaluate_term_id'];
        $termId = $this->Evaluation->getTermIdByEvaluationId($expectedId);
        $this->assertEquals($termId, $expectedTermId);
    }

    function testGetNextEvaluatorId()
    {
        $this->_setDefault();
        $this->Evaluation->deleteAll(['Evaluation.id >' => 0]);
        $this->_saveEvaluations();
        $expectedEvaluatorId = 2;

        $nextEvaluatorId = $this->Evaluation->getNextEvaluatorId($this->Evaluation->EvaluateTerm->getCurrentTermData(),
            1);
        $this->assertEquals($nextEvaluatorId, $expectedEvaluatorId);
    }

    function testGetNextEvaluatorIdNextIsNull()
    {
        $this->_setDefault();
        $this->Evaluation->deleteAll(['Evaluation.id >' => 0]);
        $this->_saveEvaluations();
        $evaluatee_user_id = 1;

        $options = [
            'conditions' => [
                'evaluate_term_id'  => $this->Evaluation->EvaluateTerm->getCurrentTermData(),
                'evaluatee_user_id' => $evaluatee_user_id
            ],
            'order'      => [
                'index_num desc',
                'id desc'
            ]
        ];
        $res = $this->Evaluation->find('first', $options);
        $lastEvaluator = $res['Evaluation']['evaluator_user_id'];

        $nextEvaluatorId = $this->Evaluation->getNextEvaluatorId($this->Evaluation->EvaluateTerm->getCurrentTermData(),
            $lastEvaluator);
        $this->assertEquals($nextEvaluatorId, null);
    }

    function testGetIsEditableCaseTermIsFrozen()
    {
        $this->_setDefault();
        $this->Evaluation->Team->current_team_id = 1;
        $this->Evaluation->Team->my_uid = 1;
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $termId = $this->Evaluation->Team->EvaluateTerm->getTermId(EvaluateTerm::TYPE_NEXT);
        $this->Evaluation->Team->EvaluateTerm->changeFreezeStatus($termId);
        $this->Evaluation->getIsEditable($termId, null);
    }

    function testGetAllStatusesForTeamSettings()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();
        $this->Evaluation->getAllStatusesForTeamSettings($this->Evaluation->EvaluateTerm->getCurrentTermData());
    }

    function testGetIncompleteEvaluatees()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();
        $this->Evaluation->getIncompleteEvaluatees($this->Evaluation->EvaluateTerm->getCurrentTermData());
    }

    function testGetIncompleteEvaluators()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();
        $this->Evaluation->getIncompleteEvaluators($this->Evaluation->EvaluateTerm->getCurrentTermData());
    }

    function testGetIncompleteNumberList()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();

        $res = $this->Evaluation->getIncompleteNumberList();
        $this->assertNotEmpty($res);
    }

    function testGetEvaluators()
    {
        $this->_setDefault();
        $this->Evaluation->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();
        $res = $this->Evaluation->getEvaluators($this->Evaluation->EvaluateTerm->getCurrentTermId(), 1);
        $this->assertNotEmpty($res);
    }

    function testGetEvaluateesByEvaluator()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();
        $evaluatorId = 2;
        $this->Evaluation->getEvaluateesByEvaluator($this->Evaluation->EvaluateTerm->getCurrentTermData(),
            $evaluatorId);
    }

    function testGetIncompleteOneselfEvaluators()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();
        $this->Evaluation->getIncompleteOneselfEvaluators($this->Evaluation->EvaluateTerm->getCurrentTermData());
    }

    function testGetFinalEvaluations()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();
        $res = $this->Evaluation->getFinalEvaluations($this->Evaluation->EvaluateTerm->getCurrentTermData(), [1, 2, 3]);
        $this->assertTrue(count($res) === 2);
    }

    function testGetEvaluateeIdsByTermId()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();
        $excepted = array(
            (int)1 => '1',
            (int)2 => '2'
        );
        $actual = $this->Evaluation->getEvaluateeIdsByTermId($this->Evaluation->EvaluateTerm->getCurrentTermData());
        $this->assertEquals($excepted, $actual);
    }

    function testGetEvaluatorIdsByTermId()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();
        $excepted = array(
            (int)1 => '1',
            (int)2 => '2',
            (int)3 => '3'
        );
        $actual = $this->Evaluation->getEvaluatorIdsByTermId($this->Evaluation->EvaluateTerm->getCurrentTermData());
        $this->assertEquals($excepted, $actual);
    }

    function testIsThisEvaluateType()
    {
        $this->_setDefault();
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->_saveEvaluations();
        $res1 = $this->Evaluation->isThisEvaluateType(1, Evaluation::TYPE_ONESELF);
        $this->assertNotEmpty($res1);
        $res2 = $this->Evaluation->isThisEvaluateType(1, Evaluation::TYPE_FINAL_EVALUATOR);
        $this->assertEmpty($res2);
    }

    function _saveEvaluations()
    {
        $evaluateeId = 1;
        $secondEvaluateeId = 2;
        $evalTermId = $this->Evaluation->EvaluateTerm->getCurrentTermId();
        $records = [
            [
                'Evaluation' => [
                    'id'                => 1,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 0,
                    'goal_id'           => null,
                    'index_num'         => 0,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 2,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 2,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 1,
                    'my_turn_flg'       => true,
                    'goal_id'           => null,
                    'index_num'         => 1,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 3,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 3,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 3,
                    'goal_id'           => null,
                    'index_num'         => 2,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 4,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 0,
                    'goal_id'           => 1,
                    'index_num'         => 3,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 5,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 2,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 1,
                    'my_turn_flg'       => true,
                    'goal_id'           => 1,
                    'index_num'         => 4,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 6,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 3,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 1,
                    'goal_id'           => 1,
                    'index_num'         => 5,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 7,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 0,
                    'goal_id'           => 2,
                    'index_num'         => 6,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 8,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 2,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 1,
                    'my_turn_flg'       => true,
                    'goal_id'           => 2,
                    'index_num'         => 7,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 9,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $evaluateeId,
                    'evaluator_user_id' => 3,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 1,
                    'goal_id'           => 2,
                    'index_num'         => 8,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 10,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $secondEvaluateeId,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 0,
                    'goal_id'           => null,
                    'index_num'         => 0,
                    'status'            => 2
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 11,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $secondEvaluateeId,
                    'evaluator_user_id' => 2,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 1,
                    'goal_id'           => null,
                    'index_num'         => 1,
                    'status'            => 2
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 12,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $secondEvaluateeId,
                    'evaluator_user_id' => 3,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 3,
                    'goal_id'           => null,
                    'index_num'         => 2,
                    'status'            => 2
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 13,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $secondEvaluateeId,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 0,
                    'goal_id'           => 3,
                    'index_num'         => 3,
                    'status'            => 2
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 14,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $secondEvaluateeId,
                    'evaluator_user_id' => 2,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 1,
                    'goal_id'           => 3,
                    'index_num'         => 4,
                    'status'            => 2
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 15,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $secondEvaluateeId,
                    'evaluator_user_id' => 3,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 1,
                    'goal_id'           => 3,
                    'index_num'         => 5,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 16,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $secondEvaluateeId,
                    'evaluator_user_id' => 1,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 0,
                    'goal_id'           => 4,
                    'index_num'         => 6,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 17,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $secondEvaluateeId,
                    'evaluator_user_id' => 2,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 1,
                    'goal_id'           => 4,
                    'index_num'         => 7,
                    'status'            => 0
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 18,
                    'team_id'           => $this->Evaluation->current_team_id,
                    'evaluatee_user_id' => $secondEvaluateeId,
                    'evaluator_user_id' => 3,
                    'evaluate_term_id'  => $evalTermId,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'evaluate_type'     => 1,
                    'goal_id'           => 4,
                    'index_num'         => 8,
                    'status'            => 0
                ],
            ],
        ];
        $this->Evaluation->saveAll($records);
    }

    function _setDefault()
    {
        $this->Evaluation->current_team_id = 1;
        $this->Evaluation->my_uid = 1;
        $this->Evaluation->Team->current_team_id = 1;
        $this->Evaluation->Team->my_uid = 1;
        $this->Evaluation->Team->TeamMember->current_team_id = 1;
        $this->Evaluation->Team->TeamMember->my_uid = 1;
        $this->Evaluation->Team->EvaluateTerm->current_team_id = 1;
        $this->Evaluation->Team->EvaluateTerm->my_uid = 1;
        $this->Evaluation->Team->Evaluator->current_team_id = 1;
        $this->Evaluation->Team->Evaluator->my_uid = 1;
        $this->Evaluation->Team->EvaluationSetting->current_team_id = 1;
        $this->Evaluation->Team->EvaluationSetting->my_uid = 1;
        $this->Evaluation->Goal->Collaborator->current_team_id = 1;
        $this->Evaluation->Goal->Collaborator->my_uid = 1;
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->Evaluation->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $this->current_date = strtotime('2015/7/1');
        $this->start_date = strtotime('2015/7/1');
        $this->end_date = strtotime('2015/10/1');
    }

}
