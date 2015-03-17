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

    const TYPE_SELF = 0;
    const TYPE_EVALUATOR = 1;
    const TYPE_LEADER = 2;
    const TYPE_FINAL = 3;
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
        'index'   => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
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

    function startEvaluation()
    {
        //get evaluation setting.
        if (!$this->Team->EvaluationSetting->isEnabled()) {
            return false;
        }
        $this->Team->EvaluateTerm->saveTerm();
        $term_id = $this->Team->EvaluateTerm->getLastInsertID();

        $eval_setting = $this->Team->EvaluationSetting->getEvaluationSetting();
        $is_enable_self = $this->Team->EvaluationSetting->isEnabledSelf();
        $is_enable_evaluator = $this->Team->EvaluationSetting->isEnabledEvaluator();
        $is_enable_leader = $this->Team->EvaluationSetting->isEnabledLeader();
        $team_members_list = $this->Team->TeamMember->getAllMemberUserIdList(true);
        if ($is_enable_evaluator) {
            $evaluators = $this->Team->Evaluator->getEvaluatorsCombined();
        }
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
                $data['evaluate_type'] = self::TYPE_SELF;
                $data['index'] = $index;
            }

            //evaluator total

            //final total

            /**
             * goal evaluation
             */
            //self
            //evaluator
            //leader

        }

        return true;
    }

}
