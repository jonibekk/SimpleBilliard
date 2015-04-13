<?php

class AddApprovalHistories0413 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_approval_histories_0413';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'approval_histories' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'collaborator_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コラボレーターID(hasManyでcollaboratorモデルに関連)'),
                    'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザーID(belongsToでUserモデルに関連)'),
                    'comment'         => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント', 'charset' => 'utf8'),
                    'action_status'   => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => ' 状態(0 = アクションなし,1 =コメントのみ, 2 = 評価対象にする, 3 = 評価対象にしない, 4 =修正依頼)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY'         => array('column' => 'id', 'unique' => 1),
                        'collaborator_id' => array('column' => 'collaborator_id', 'unique' => 0),
                        'del_flg'         => array('column' => 'del_flg', 'unique' => 0),
                        'created'         => array('column' => 'created', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'drop_table' => array(
                'approval_histories'
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
