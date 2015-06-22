<?php

class AddTablesForVision0622 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_tables_for_vision_0622';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'group_visions' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'name'            => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'グループビジョン名', 'charset' => 'utf8'),
                    'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'グループビジョンの説明', 'charset' => 'utf8'),
                    'create_user_id'  => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ユーザID(belongsToでUserモデルに関連)'),
                    'modify_user_id'  => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '最終編集者ユーザID(belongsToでUserモデルに関連)'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'group_id'        => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'グループID(belongsToでGroupモデルに関連)'),
                    'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(0の場合はアーカイブ)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY'        => array('column' => 'id', 'unique' => 1),
                        'create_user_id' => array('column' => 'create_user_id', 'unique' => 0),
                        'modify_user_id' => array('column' => 'modify_user_id', 'unique' => 0),
                        'group_id'       => array('column' => 'group_id', 'unique' => 0),
                        'team_id'        => array('column' => 'team_id', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'team_visions'  => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'name'            => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'チームビジョン名', 'charset' => 'utf8'),
                    'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'チームビジョンの説明', 'charset' => 'utf8'),
                    'create_user_id'  => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ユーザID(belongsToでUserモデルに関連)'),
                    'modify_user_id'  => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '最終編集者ユーザID(belongsToでUserモデルに関連)'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(0の場合はアーカイブ)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY'        => array('column' => 'id', 'unique' => 1),
                        'create_user_id' => array('column' => 'create_user_id', 'unique' => 0),
                        'modify_user_id' => array('column' => 'modify_user_id', 'unique' => 0),
                        'team_id'        => array('column' => 'team_id', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'group_visions', 'team_visions'
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
