<?php

class RemaneRatersToEvaluators0310 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'remane_raters_to_evaluators_0310';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = [
        'up'   => [
            'rename_table' => [
                'raters' => 'evaluators',
            ],
            'drop_field'   => [
                'evaluators' => [
                    'indexes' => ['rater_user_id', 'ratee_user_id',]
                ],
            ],
            'rename_field' => [
                'evaluators' => [
                    'rater_user_id' => 'evaluator_user_id',
                    'ratee_user_id' => 'evaluatee_user_id',
                ]
            ],
            'create_field' => [
                'evaluators' => [
                    'indexes' => [
                        'evaluator_user_id' => ['column' => 'evaluator_user_id', 'unique' => 0],
                        'evaluatee_user_id' => ['column' => 'evaluatee_user_id', 'unique' => 0],
                    ],
                ],
            ],
        ],
        'down' => [
            'rename_table' => [
                'evaluators' => 'raters',
            ],
            'drop_field'   => [
                'raters' => [
                    'indexes' => ['evaluator_user_id', 'evaluatee_user_id',],
                ],
            ],
            'rename_field' => [
                'raters' => [
                    'evaluator_user_id' => 'rater_user_id',
                    'evaluatee_user_id' => 'ratee_user_id',
                ]
            ],
            'create_field' => [
                'raters' => [
                    'indexes' => [
                        'rater_user_id' => ['column' => 'rater_user_id', 'unique' => 0],
                        'ratee_user_id' => ['column' => 'ratee_user_id', 'unique' => 0],
                    ]
                ],
            ],
        ],
    ];

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
