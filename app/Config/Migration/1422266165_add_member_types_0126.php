<?php

class AddMemberTypes0126 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_member_types_0126';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'member_types' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '部署ID'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'タイプ名(正社員等', 'charset' => 'utf8'),
                    'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイプの説明(正規雇用で企業に雇われた労働者等', 'charset' => 'utf8'),
                    'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY'    => array('column' => 'id', 'unique' => 1),
                        'team_id'    => array('column' => 'team_id', 'unique' => 0),
                        'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
            'create_field' => array(
                'team_members' => array(
                    'member_type_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メンバータイプID(belongsToでmember_typesモデルに関連)', 'after' => 'member_no'),
                    'indexes'        => array(
                        'member_type_id' => array('column' => 'member_type_id', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'member_types'
            ),
            'drop_field' => array(
                'team_members' => array('member_type_id', 'indexes' => array('member_type_id')),
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
