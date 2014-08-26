<?php

class CreateNotifySettings0814 extends CakeMigration
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
            'create_table' => array(
                'notify_settings' => array(
                    'id'               => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'user_id'          => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                    'feed_app_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '投稿アプリ通知'),
                    'feed_email_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '投稿メール通知'),
                    'circle_app_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'サークル アプリ通知'),
                    'circle_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'サークル メール通知'),
                    'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '登録した日付時刻'),
                    'modified'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'          => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'del_flg' => array('column' => 'del_flg', 'unique' => 0),
                    ),
                    'tableParameters'  => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'notify_settings'
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
