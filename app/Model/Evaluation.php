<?php
App::uses('AppModel', 'Model');

/**
 * Evaluation Model
 *
 * @property Team          $Team
 * @property User          $EvaluateeUser
 * @property User          $EvaluatorUser
 * @property EvaluateTerm  $EvaluateTerm
 * @property EvaluateScore $EvaluateScore
 * @property Goal          $Goal
 */
class Evaluation extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'index'             => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'           => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluatee_user_id' => [
            'notEmpty' => [
                'rule' => 'notEmpty'
            ]
        ],
        'evaluator_user_id' => [
            'notEmpty' => [
                'rule' => 'notEmpty'
            ]
        ],
        'evaluate_term_id'  => [
            'notEmpty' => [
                'rule' => 'notEmpty'
            ]
        ],
        'comment'           => [],
        'evaluate_score_id' => []
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team',
        'EvaluateeUser' => [
            'className'  => 'User',
            'foreignKey' => 'evaluatee_user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ],
        'EvaluatorUser' => [
            'className'  => 'User',
            'foreignKey' => 'evaluator_user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ],
        'EvaluateTerm',
        'EvaluateScore',
        'Goal',
    ];

    /**
     * evaluation type
     */
    const TYPE_ONESELF = 0;
    const TYPE_EVALUATOR = 1;
    const TYPE_LEADER = 2;
    const TYPE_FINAL_EVALUATOR = 3;

    /**
     *  status type
     */
    const TYPE_STATUS_NOT_ENTERED = 0;
    const TYPE_STATUS_DRAFT = 1;
    const TYPE_STATUS_DONE = 2;

    var $evaluationType = null;

    /**
     * 評価リストの閲覧権限チェック
     * ・評価画面の表示条件
     * 　チームの評価機能がon かつ 自分の評価フラグがon
     *
     * @return bool
     */
    function checkAvailViewEvaluationList()
    {
        $my_team_member_status = $this->Team->TeamMember->getWithTeam();
        if (!viaIsSet($my_team_member_status['TeamMember'])) {
            throw new RuntimeException(__d('gl', "この画面にはアクセスできません。"));
        }
        if (!$my_team_member_status['TeamMember']['evaluation_enable_flg']) {
            throw new RuntimeException(__d('gl', "評価設定がoffになっています。チーム管理者にご確認ください"));
        }
        return true;
    }

    function getMyEvaluation()
    {
        $options = [
            'conditions' => [
                'evaluatee_user_id' => $this->my_uid,
                'team_id'           => $this->current_team_id
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    public function add($data, $saveType)
    {
        // insert status value to save data
        if ($saveType === "draft") {
            $data = Hash::insert($data, '{n}.Evaluation.status', 1);
            $this->setDraftValidation();
        }
        else {
            $data = Hash::insert($data, '{n}.Evaluation.status', 2);
            $this->setNotAllowEmptyToComment();
            $this->setNotAllowEmptyToEvaluateScoreId();
        }

        foreach ($data as $key => $law) {
            $this->create();
            $law['Evaluation']['index'] = $key;
            if (!$this->save($law)) {
                if (!empty($this->validationErrors)) {
                    throw new RuntimeException(__d('validate', "入力内容に不足があります。"));
                }
                else {
                    throw new RuntimeException(__d('validate', "保存処理に失敗しました。"));
                }
            }
        }

        return true;
    }

    public function getEditableEvaluations($evaluateTermId, $evaluateeId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id'  => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId,
                'OR'                => [
                    ['Evaluation.status' => self::TYPE_STATUS_NOT_ENTERED],
                    ['Evaluation.status' => self::TYPE_STATUS_DRAFT]
                ]
            ],
            'order'      => 'Evaluation.index asc',
            'contain'    => [
                'Goal' => [
                    'KeyResult',
                    'GoalCategory',
                    'MyCollabo'
                ]
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    public function setDraftValidation()
    {
        $this->setAllowEmptyToComment();
        $this->setAllowEmptyToEvaluateScoreId();
        return;
    }

    public function setAllowEmptyToComment()
    {
        if (isset($this->validate['comment']['notEmpty'])) {
            unset($this->validate['comment']['notEmpty']);
        }
        return;
    }

    public function setNotAllowEmptyToComment()
    {
        if (isset($this->validate['comment']['notEmpty'])) {
            return;
        }
        $this->validate['comment']['notEmpty'] = ['rule' => 'notEmpty'];
        return;
    }

    public function setAllowEmptyToEvaluateScoreId()
    {
        if (isset($this->validate['evaluate_score_id']['notEmpty'])) {
            unset($this->validate['evaluate_score_id']['notEmpty']);
        }
        return;
    }

    public function setNotAllowEmptyToEvaluateScoreId()
    {
        if (isset($this->validate['evaluate_score_id']['notEmpty'])) {
            return;
        }
        $this->validate['evaluate_score_id']['notEmpty'] = ['rule' => 'notEmpty'];
        return;
    }

    /**
     * @return bool
     */
    function startEvaluation()
    {
        //get evaluation setting.
        if (!$this->Team->EvaluationSetting->isEnabled()) {
            return false;
        }
        $this->Team->EvaluateTerm->saveTerm();
        $term_id = $this->Team->EvaluateTerm->getLastInsertID();

        $is_enable_self = $this->Team->EvaluationSetting->isEnabledSelf();
        $is_enable_evaluator = $this->Team->EvaluationSetting->isEnabledEvaluator();
        $is_enable_leader = $this->Team->EvaluationSetting->isEnabledLeader();
        $is_enable_final = $this->Team->EvaluationSetting->isEnabledFinal();
        $team_members_list = $this->Team->TeamMember->getAllMemberUserIdList(true, true, true);
        $evaluators = [];
        if ($is_enable_evaluator) {
            $evaluators = $this->Team->Evaluator->getEvaluatorsCombined();
        }
        $admin_uid = $this->Team->TeamMember->getTeamAdminUid();
        $all_evaluations = [];
        //一人ずつデータを生成
        foreach ($team_members_list as $uid) {
            $index = 0;
            $default_data = [
                'evaluatee_user_id' => $uid,
                'team_id'           => $this->current_team_id,
                'evaluator_user_id' => null,
                'goal_id'           => null,
                'evaluate_term_id'  => $term_id,
                'evaluate_type'     => null,
                'index'             => 0,
            ];
            //self total
            if ($is_enable_self) {
                $data = $default_data;
                $data['evaluator_user_id'] = $uid;
                $data['evaluate_type'] = self::TYPE_ONESELF;
                $data['index'] = $index++;
                $all_evaluations[] = $data;
            }
            //evaluator total
            if ($is_enable_evaluator && viaIsSet($evaluators[$uid])) {
                $evals = $evaluators[$uid];
                foreach ($evals as $eval_uid) {
                    $data = $default_data;
                    $data['evaluator_user_id'] = $eval_uid;
                    $data['evaluate_type'] = self::TYPE_EVALUATOR;
                    $data['index'] = $index++;
                    $all_evaluations[] = $data;
                }
            }
            //final total
            if ($is_enable_final && $admin_uid) {
                $data = $default_data;
                $data['evaluator_user_id'] = $admin_uid;
                $data['evaluate_type'] = self::TYPE_FINAL_EVALUATOR;
                $data['index'] = $index++;
                $all_evaluations[] = $data;
            }

            /**
             * goal evaluation
             */
            $goal_list = $this->Goal->Collaborator->getCollaboGoalList($uid, true);
            foreach ($goal_list as $gid) {
                //self
                if ($is_enable_self) {
                    $data = $default_data;
                    $data['evaluator_user_id'] = $uid;
                    $data['evaluate_type'] = self::TYPE_ONESELF;
                    $data['goal_id'] = $gid;
                    $data['index'] = $index++;
                    $all_evaluations[] = $data;
                }

                //evaluator
                if ($is_enable_evaluator && viaIsSet($evaluators[$uid])) {
                    $evals = $evaluators[$uid];
                    foreach ($evals as $eval_uid) {
                        $data = $default_data;
                        $data['evaluator_user_id'] = $eval_uid;
                        $data['evaluate_type'] = self::TYPE_EVALUATOR;
                        $data['goal_id'] = $gid;
                        $data['index'] = $index++;
                        $all_evaluations[] = $data;
                    }
                }
                //leader
                if ($is_enable_leader) {
                    $leader_uid = $this->Goal->Collaborator->getLeaderUid($gid);
                    if ($uid !== $leader_uid) {
                        $data = $default_data;
                        $data['evaluator_user_id'] = $leader_uid;
                        $data['evaluate_type'] = self::TYPE_LEADER;
                        $data['goal_id'] = $gid;
                        $data['index'] = $index++;
                        $all_evaluations[] = $data;
                    }
                }
            }
        }
        if (!empty($all_evaluations)) {
            $res = $this->saveAll($all_evaluations);
            return (bool)$res;
        }
        return false;
    }

}
