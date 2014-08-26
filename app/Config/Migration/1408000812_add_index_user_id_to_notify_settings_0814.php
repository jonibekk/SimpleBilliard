<?php

class AddIndexUserIdToNotifySettings0814 extends CakeMigration
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
            'alter_field'  => array(
                'notify_settings' => array(
                    'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                ),
            ),
            'create_field' => array(
                'notify_settings' => array(
                    'indexes' => array(
                        'user_id' => array('column' => 'user_id', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'notify_settings' => array(
                    'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                ),
            ),
            'drop_field'  => array(
                'notify_settings' => array('', 'indexes' => array('user_id')),
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
