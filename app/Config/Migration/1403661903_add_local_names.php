<?php

class AddLocalNames extends CakeMigration
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
                'local_names' => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'ローカル名ID', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'language'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8'),
                    'first_name'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '名', 'charset' => 'utf8'),
                    'last_name'       => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '姓', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを登録した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを最後に更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'user_id' => array('column' => 'user_id', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
            'drop_field'   => array(
                'users' => array('local_first_name', 'local_last_name', 'local_middle_name',),
            ),
            'alter_field'  => array(
                'users' => array(
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'プロフィール画像', 'charset' => 'utf8'),
                ),
            ),
        ),
        'down' => array(
            'drop_table'   => array(
                'local_names'
            ),
            'create_field' => array(
                'users' => array(
                    'local_first_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '母国の名', 'charset' => 'utf8'),
                    'local_last_name'   => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '母国の姓', 'charset' => 'utf8'),
                    'local_middle_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '母国のミドルネーム', 'charset' => 'utf8'),
                ),
            ),
            'alter_field'  => array(
                'users' => array(
                    'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
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
