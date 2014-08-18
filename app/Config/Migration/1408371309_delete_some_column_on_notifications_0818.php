<?php

class DeleteSomeColumnOnNotifications0818 extends CakeMigration
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
            'drop_field' => array(
                'notifications' => array('user_id', 'unread_flg', 'enable_flg', 'indexes' => array('user_id', 'unread_flg', 'enable_flg')),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'notifications' => array(
                    'user_id'    => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                    'unread_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => '未読フラグ(通知を開いたらOff)'),
                    'enable_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => '有効フラグ(通知設定offの場合はfalse)'),
                    'indexes'    => array(
                        'user_id'    => array('column' => 'user_id', 'unique' => 0),
                        'unread_flg' => array('column' => 'unread_flg', 'unique' => 0),
                        'enable_flg' => array('column' => 'enable_flg', 'unique' => 0),
                    ),
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
