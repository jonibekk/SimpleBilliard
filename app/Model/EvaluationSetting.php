<?php
App::uses('AppModel', 'Model');

/**
 * EvaluationSetting Model
 *
 * @property Team $Team
 */
class EvaluationSetting extends AppModel
{

    const FLG_ENABLE = 'enable_flg';
    const FLG_SELF = 'self_flg';
    const FLG_EVALUATOR = 'evaluator_flg';
    const FLG_FINAL = 'final_flg';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'enable_flg'                          => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'self_flg'                            => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'self_goal_score_flg'                 => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'self_goal_score_required_flg'        => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'self_goal_comment_flg'               => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'self_goal_comment_required_flg'      => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'self_score_flg'                      => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'self_score_required_flg'             => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'self_comment_flg'                    => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'self_comment_required_flg'           => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluator_flg'                       => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluator_goal_score_flg'            => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluator_goal_score_reuqired_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluator_goal_comment_flg'          => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluator_goal_comment_required_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluator_score_flg'                 => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluator_score_required_flg'        => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluator_comment_flg'               => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluator_comment_required_flg'      => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'final_flg'                           => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'final_score_flg'                     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'final_score_required_flg'            => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'final_comment_flg'                   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'final_comment_required_flg'          => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'leader_flg'                          => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'leader_goal_score_flg'               => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'leader_goal_score_reuqired_flg'      => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'leader_goal_comment_flg'             => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'leader_goal_comment_required_flg'    => [
            'boolean' => [
                'rule' => ['boolean'],
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

    public function getSettings($teamId) {
        $options = [
            'conditions' => [
                'team_id'                  => $teamId,
            ],
        ];
        return $this->find('first', $options);
    }

    public function getSelfSettings($teamId) {

    }

    public function getEvaluatorSettings($teamId) {

    }

    public function getFinalSettings($teamId) {

    }

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
        $this->evaluation_setting = $this->find('first', $options);
        if (empty($this->evaluation_setting)) {
            $this->not_exists_evaluation_setting = true;
        }
        return $this->evaluation_setting;
    }
}
