<?php

class MovePriotiryAndValuedFlg1024 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = '';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'collaborators' => array(
                    'priority'   => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)', 'after' => 'description'),
                    'valued_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '価値フラグ', 'after' => 'priority'),
                ),
            ),
            'drop_field'   => array(
                'goals' => array('priority',),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'collaborators' => array('priority', 'valued_flg',),
            ),
            'create_field' => array(
                'goals' => array(
                    'priority' => array('type' => 'integer', 'null' => false, 'default' => '3', 'unsigned' => false, 'comment' => '重要度(1〜5)'),
                ),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
