<?php

class AddSubscribeEmails extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_subscribe_emails';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'subscribe_emails' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'email'           => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '登録した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '最後に更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'email'   => array('column' => 'email', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'subscribe_emails'
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
