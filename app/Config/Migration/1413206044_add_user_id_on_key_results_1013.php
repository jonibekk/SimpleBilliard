<?php

class AddUserIdOnKeyResults1013 extends CakeMigration
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
                'key_results' => array(
                    'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)', 'after' => 'goal_id'),
                    'indexes' => array(
                        'user_id' => array('column' => 'user_id', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'key_results' => array('user_id', 'indexes' => array('user_id')),
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
