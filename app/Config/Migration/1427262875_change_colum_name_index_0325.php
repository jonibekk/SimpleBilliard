<?php

class ChangeColumNameIndex0325 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_colum_name_index_0325';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = [
        'up'   => [
            'rename_field' => [
                'evaluate_scores' => ['index' => 'index_num'],
                'evaluations'     => ['index' => 'index_num'],
                'evaluators'      => ['index' => 'index_num'],
                'member_groups'   => ['index' => 'index_num'],
            ],
        ],
        'down' => [
            'rename_field' => [
                'evaluate_scores' => ['index_num' => 'index'],
                'evaluations'     => ['index_num' => 'index'],
                'evaluators'      => ['index_num' => 'index'],
                'member_groups'   => ['index_num' => 'index'],
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
