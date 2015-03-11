<?php
App::uses('AppModel', 'Model');

/**
 * EvaluationSetting Model
 *
 * @property Team $Team
 */
class EvaluationSetting extends AppModel
{

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

}
