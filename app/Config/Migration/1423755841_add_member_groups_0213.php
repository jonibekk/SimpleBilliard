<?php

class AddMemberGroups0213 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_member_groups_0213';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'drop_field'   => array(
                'groups'       => array('parent_id', 'indexes' => array('parent_id')),
                'team_members' => array('group_id', 'indexes' => array('group_id')),
            ),
            'create_table' => array(
                'member_groups' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                    'group_id'        => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'グループID(belongsToでGroupモデルに関連)'),
                    'index'           => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'グループの順序'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY'  => array('column' => 'id', 'unique' => 1),
                        'team_id'  => array('column' => 'team_id', 'unique' => 0),
                        'user_id'  => array('column' => 'user_id', 'unique' => 0),
                        'group_id' => array('column' => 'group_id', 'unique' => 0),
                        'del_flg'  => array('column' => 'del_flg', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'create_field' => array(
                'groups'       => array(
                    'parent_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '上位部署ID(belongsToで同モデルに関連)'),
                    'indexes'   => array(
                        'parent_id' => array('column' => 'parent_id', 'unique' => 0),
                    ),
                ),
                'team_members' => array(
                    'group_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '部署ID(belongsToでgroupモデルに関連)'),
                    'indexes'  => array(
                        'group_id' => array('column' => 'group_id', 'unique' => 0),
                    ),
                ),
            ),
            'drop_table'   => array(
                'member_groups'
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
