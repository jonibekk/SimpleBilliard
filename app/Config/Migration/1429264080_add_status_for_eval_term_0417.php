<?php

class AddStatusForEvalTerm0417 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_status_for_eval_term_0417';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'evaluate_terms' => array(
                    'evaluate_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価ステータス(0 = 評価中, 1 = 最終評価終了)', 'after' => 'end_date'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'evaluate_terms' => array('evaluate_status'),
            ),
        ),
    );

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
