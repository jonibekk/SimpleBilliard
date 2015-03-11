<?php

class AddDraftFlgToEvaluations0311 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_draft_flg_to_evaluations_0311';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'evaluations' => array(
                    'draft_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '下書きフラグ', 'after' => 'index'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'evaluations' => array('draft_flg'),
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
