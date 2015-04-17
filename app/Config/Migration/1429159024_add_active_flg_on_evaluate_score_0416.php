<?php

class AddActiveFlgOnEvaluateScore0416 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_active_flg_on_evaluate_score_0416';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'evaluate_scores' => array(
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)', 'after' => 'index_num'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'evaluate_scores' => array('active_flg'),
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
