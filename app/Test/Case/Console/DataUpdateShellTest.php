<?php
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('DataUpdateShell', 'Console/Command');
App::uses('Evaluation', 'Model');


class DataUpdateShellTest extends CakeTestCase {
    public $DataUpdateShell;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluation',
        'app.team',
        'app.user',
        'app.goal',
        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.collaborator',
        'app.follower',
        'app.team_member',
        'app.member_type',
        'app.local_name',
        'app.evaluator',
        'app.evaluate_term',
        'app.evaluate_score',
        'app.evaluation_setting'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();

        $output = $this->getMock('ConsoleOutput', [], [], '', false);
        $error = $this->getMock('ConsoleOutput', [], [], '', false);
        $in = $this->getMock('ConsoleInput', [], [], '', false);
        $this->DataUpdateShell = new DataUpdateShell($output, $error, $in);
        $this->DataUpdateShell->initialize();
        $this->Evaluation = ClassRegistry::init('Evaluation');
    }

    public function tearDown() {

    }

    public function testConstruct() {
        $this->assertEquals('DataUpdate', $this->DataUpdateShell->name);
    }

    /**
     * index_numが正しく再割当されているかチェック
     */
    public function testAssignedIndexCorrectly() {
        $this->_setDefault();
        $this->DataUpdateShell->recovery_evaluations();
        $res = $this->Evaluation->find("all");
        // Grouping as evaluatee
        $res_combined = Hash::combine($res, "{n}.Evaluation.id", "{n}.Evaluation.index_num", "{n}.Evaluation.evaluatee_user_id");
        foreach($res_combined as $group) {
            // Initialize array index key
            $group = array_values($group);
            for($i=0;$i<count($group);$i++){
                $this->assertEquals($group[$i], $i);
            }
        }
    }

    /**
     * my_turn_flgが正しく立てられているかチェック
     */
    public function testAddFlgToEvaluateeCorrectly(){
        $this->_setDefault();
        $this->DataUpdateShell->recovery_evaluations();
        $res = $this->Evaluation->find("all");
        // Grouping as evaluatee
        $res_combined = Hash::combine($res, "{n}.Evaluation.id", "{n}.Evaluation");
        foreach($res_combined as $key => $eval) {

            // case of evaluatee
            if($eval['evaluate_type'] == $this->Evaluation->TYPE_ONESELF) {
                $evaluateeNotRegister = $eval['status'] ==
                    $this->Evaluation->TYPE_STATUS_NOT_ENTERED || $eval['status'] == TYPE_STATUS_DRAFT;
                $myTurnFlgExpected = $evaluateeNotRegister ? true : false;
                $this->assertEquals($eval['my_turn_flg'], $myTurnFlgExpected);

            // case of first evaluator
            } elseif($eval['evaluate_type'] == $this->Evaluation->TYPE_EVALUATOR
                && $eval['index_num'] == 1) {
                $evaluatee = $res_combined[$key - 1];
                $evaluateeRegistered = $evaluatee['status'] == $this->Evaluation->TYPE_STATUS_DONE;
                $myTurnFlgExpected = $evaluateeRegistered ? true : false;
                $this->assertEquals($eval['my_turn_flg'], $myTurnFlgExpected);
            }
        }
//        foreach($res_combined as $group) {
//            // Initialize array index key
//            $group = array_values($group);
//            for($i=0;$i<count($group);$i++){
//                $this->assertEquals($group[$i], $i);
//            }
//        }
    }

    public function _setDefault(){
        $this->Evaluation->deleteAll(['Evaluation.id >' => 0]);

        // Collapsed data
        $records = array(
            (int) 0 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '1',
                    'evaluator_user_id' => '1',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '0',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 1 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '1',
                    'evaluator_user_id' => '1',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '3',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '1',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 2 => array(
                'Evaluation' => array(
                    'id' => '871',
                    'team_id' => '1',
                    'evaluatee_user_id' => '2',
                    'evaluator_user_id' => '2',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => null,
                    'comment' => 'test',
                    'evaluate_score_id' => '1',
                    'index_num' => '0',
                    'status' => '2',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880574'
                )
            ),
            (int) 3 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '2',
                    'evaluator_user_id' => '4',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '1',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 4 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '2',
                    'evaluator_user_id' => '5',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '2',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 5 => array(
                'Evaluation' => array(
                    'id' => '874',
                    'team_id' => '1',
                    'evaluatee_user_id' => '2',
                    'evaluator_user_id' => '1',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '3',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '3',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 6 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '2',
                    'evaluator_user_id' => '2',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => '20',
                    'comment' => 'test',
                    'evaluate_score_id' => '5',
                    'index_num' => '1',
                    'status' => '2',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880574'
                )
            ),
            (int) 7 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '2',
                    'evaluator_user_id' => '4',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => '20',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '5',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 8 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '2',
                    'evaluator_user_id' => '5',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => '20',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '6',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 9 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '2',
                    'evaluator_user_id' => '2',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => '21',
                    'comment' => 'est',
                    'evaluate_score_id' => '1',
                    'index_num' => '2',
                    'status' => '2',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880574'
                )
            ),
            (int) 10 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '2',
                    'evaluator_user_id' => '4',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => '21',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '8',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 11 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '2',
                    'evaluator_user_id' => '5',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => '21',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '9',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 12 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '3',
                    'evaluator_user_id' => '3',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => null,
                    'comment' => 'test',
                    'evaluate_score_id' => '2',
                    'index_num' => '0',
                    'status' => '2',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880661'
                )
            ),
            (int) 13 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '3',
                    'evaluator_user_id' => '2',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '1',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 14 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '3',
                    'evaluator_user_id' => '1',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '3',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '2',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 15 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '3',
                    'evaluator_user_id' => '3',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => '22',
                    'comment' => 'test',
                    'evaluate_score_id' => '3',
                    'index_num' => '1',
                    'status' => '2',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880661'
                )
            ),
            (int) 16 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '3',
                    'evaluator_user_id' => '2',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => '22',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '4',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 17 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '3',
                    'evaluator_user_id' => '3',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => '23',
                    'comment' => 'test',
                    'evaluate_score_id' => '4',
                    'index_num' => '2',
                    'status' => '2',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880661'
                )
            ),
            (int) 18 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '3',
                    'evaluator_user_id' => '2',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => '23',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '6',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 19 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '4',
                    'evaluator_user_id' => '4',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '0',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 20 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '4',
                    'evaluator_user_id' => '1',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '1',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 21 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '4',
                    'evaluator_user_id' => '2',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '2',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 22 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '4',
                    'evaluator_user_id' => '3',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '3',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 23 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '4',
                    'evaluator_user_id' => '1',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '3',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '4',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 24 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '4',
                    'evaluator_user_id' => '4',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => '24',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '5',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 25 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '4',
                    'evaluator_user_id' => '1',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => '24',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '6',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 26 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '4',
                    'evaluator_user_id' => '2',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => '24',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '7',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 27 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '4',
                    'evaluator_user_id' => '3',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '1',
                    'goal_id' => '24',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '8',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 28 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '5',
                    'evaluator_user_id' => '5',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '0',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 29 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '5',
                    'evaluator_user_id' => '1',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '3',
                    'goal_id' => null,
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '1',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            ),
            (int) 30 => array(
                'Evaluation' => array(
                    'team_id' => '1',
                    'evaluatee_user_id' => '5',
                    'evaluator_user_id' => '5',
                    'evaluate_term_id' => '19',
                    'evaluate_type' => '0',
                    'goal_id' => '25',
                    'comment' => null,
                    'evaluate_score_id' => null,
                    'index_num' => '2',
                    'status' => '0',
                    'my_turn_flg' => false,
                    'del_flg' => false,
                    'deleted' => null,
                    'created' => '1427880539',
                    'modified' => '1427880539'
                )
            )
        );
        $this->Evaluation->saveAll($records);
    }
}