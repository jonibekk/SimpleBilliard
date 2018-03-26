<?php
App::uses('AppModel', 'Model');

/**
 * EvaluationSetting Model
 * TODO: make flag management more efficient
 *
 * @property Team $Team
 */
class EvaluationSetting extends AppModel
{
    const FLG_ENABLE = 'enable_flg';
    const FLG_SELF = 'self_flg';
    const FLG_EVALUATOR = 'evaluator_flg';
    const FLG_FINAL = 'final_flg';
    const FLG_LEADER = 'leader_flg';
    const FLG_FIXED_EVALUATION_ORDER_FLG = 'fixed_evaluation_order_flg';
    const FLG_SHOW_ALL_EVALUATION_BEFORE_FREEZE_FLG = 'show_all_evaluation_before_freeze_flg';
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'enable_flg'                          => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'self_flg'                            => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'self_goal_score_flg'                 => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'self_goal_score_required_flg'        => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'self_goal_comment_flg'               => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'self_goal_comment_required_flg'      => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'self_score_flg'                      => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'self_score_required_flg'             => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'self_comment_flg'                    => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'self_comment_required_flg'           => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'evaluator_flg'                       => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'evaluator_goal_score_flg'            => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'evaluator_goal_score_reuqired_flg'   => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'evaluator_goal_comment_flg'          => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'evaluator_goal_comment_required_flg' => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'evaluator_score_flg'                 => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'evaluator_score_required_flg'        => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'evaluator_comment_flg'               => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'evaluator_comment_required_flg'      => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'final_flg'                           => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'final_score_flg'                     => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'final_score_required_flg'            => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'final_comment_flg'                   => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'final_comment_required_flg'          => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'leader_flg'                          => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'leader_goal_score_flg'               => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'leader_goal_score_reuqired_flg'      => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'leader_goal_comment_flg'             => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'leader_goal_comment_required_flg'    => [
            'boolean' => [
                'rule'       => ['boolean'],
                'allowEmpty' => true,
            ],
        ],
        'del_flg'                             => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * @var array
     */
    private $evaluation_setting = [];
    private $not_exists_evaluation_setting = false;

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team',
    ];

    /**
     * @return bool
     */
    function isEnabled()
    {
        return $this->isFlagOn(self::FLG_ENABLE);
    }

    /**
     * @return bool
     */
    function isEnabledSelf()
    {
        return $this->isFlagOn(self::FLG_SELF);
    }

    /**
     * @return bool
     */
    function isEnabledEvaluator()
    {
        return $this->isFlagOn(self::FLG_EVALUATOR);
    }

    /**
     * @return bool
     */
    function isEnabledFinal()
    {
        return $this->isFlagOn(self::FLG_FINAL);
    }

    /**
     * @return bool
     */
    function isEnabledLeader()
    {
        return $this->isFlagOn(self::FLG_LEADER);
    }

    /**
     * @return bool
     */
    function isFixedEvaluationOrder(): bool
    {
        return $this->isFlagOn(self::FLG_FIXED_EVALUATION_ORDER_FLG);
    }

    /**
     * @return bool
     */
    function isShowAllEvaluationBeforeFreeze(): bool
    {
        return $this->isFlagOn(self::FLG_SHOW_ALL_EVALUATION_BEFORE_FREEZE_FLG);
    }

    private function isFlagOn($flag_name)
    {
        $evaluation_setting = $this->getEvaluationSetting();
        if ($this->not_exists_evaluation_setting) {
            return false;
        }
        return (bool)$evaluation_setting['EvaluationSetting'][$flag_name];

    }

    /**
     * @return array
     */
    function getEvaluationSetting()
    {
        if (!empty($this->evaluation_setting) || $this->not_exists_evaluation_setting) {
            return $this->evaluation_setting;
        }

        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id
            ]
        ];
        $model = $this;
        $this->evaluation_setting = Cache::remember(
            $this->getCacheKey(CACHE_KEY_TEAM_EVAL_SETTING, false),
            function () use ($model, $options) {
                return $model->find('first', $options);
            }, 'team_info');

        if (empty($this->evaluation_setting)) {
            $this->not_exists_evaluation_setting = true;
        }
        return $this->evaluation_setting;
    }
}
