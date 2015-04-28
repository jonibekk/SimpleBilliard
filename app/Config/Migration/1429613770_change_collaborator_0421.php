<?php

class ChangeCollaborator0421 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_collaborator_0421';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field' => array(
                'evaluate_terms' => array(
                    'evaluate_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価ステータス(0 = 評価開始前, 1 = 評価中,2 = 評価凍結中, 3 = 最終評価終了)'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'evaluate_terms' => array(
                    'evaluate_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価ステータス(0 = 評価中, 1 = 最終評価終了)'),
                ),
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
