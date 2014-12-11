<?php

class AddActionCount1121 extends CakeMigration
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
                'actions'     => array(
                    'action_result_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'アクションリザルトカウント', 'after' => 'sun_flg'),
                ),
                'goals'       => array(
                    'action_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'アクショントカウント', 'after' => 'completed'),
                ),
                'key_results' => array(
                    'action_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'アクショントカウント', 'after' => 'completed'),
                ),
            ),
            'alter_field'  => array(
                'goals' => array(
                    'completed' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
                ),
            ),
        ),
        'down' => array(
            'drop_field'  => array(
                'actions'     => array('action_result_count',),
                'goals'       => array('action_count',),
                'key_results' => array('action_count',),
            ),
            'alter_field' => array(
                'goals' => array(
                    'completed' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '完了日'),
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
