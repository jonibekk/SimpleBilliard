<?php

class AddIndexToAllFlg0708 extends CakeMigration
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
                'badges'         => array(
                    'type'       => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
                ),
                'groups'         => array(
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
                ),
                'job_categories' => array(
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
                ),
                'notifications'  => array(
                    'unread_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => '未読フラグ(通知を開いたらOff)'),
                ),
                'posts'          => array(
                    'type'          => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
                    'public_flg'    => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index'),
                    'important_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
                ),
                'team_members'   => array(
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => '有効フラグ(Offの場合はチームにログイン不可。チームメンバーによる当該メンバーのチーム内のコンテンツへのアクセスは可能。当該メンバーへの如何なる発信は不可)'),
                    'admin_flg'  => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => 'チーム管理者フラグ(Onの場合はチーム設定が可能)'),
                ),
                'threads'        => array(
                    'type'   => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドタイプ(1:ゴール作成,2:Feedback)'),
                    'status' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドステータス(1:Open,2:Close)'),
                ),
                'users'          => array(
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => 'アクティブフラグ(ユーザ認証済みの場合On)'),
                ),
            ),
            'create_field' => array(
                'badges'         => array(
                    'indexes' => array(
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                        'type'       => array('column' => 'type', 'unique' => 0),
                    ),
                ),
                'groups'         => array(
                    'indexes' => array(
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                    ),
                ),
                'job_categories' => array(
                    'indexes' => array(
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                    ),
                ),
                'notifications'  => array(
                    'indexes' => array(
                        'unread_flg' => array('column' => 'unread_flg', 'unique' => 0),
                    ),
                ),
                'posts'          => array(
                    'indexes' => array(
                        'type'          => array('column' => 'type', 'unique' => 0),
                        'public_flg'    => array('column' => 'public_flg', 'unique' => 0),
                        'important_flg' => array('column' => 'important_flg', 'unique' => 0),
                    ),
                ),
                'team_members'   => array(
                    'indexes' => array(
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                        'admin_flg'  => array('column' => 'admin_flg', 'unique' => 0),
                    ),
                ),
                'threads'        => array(
                    'indexes' => array(
                        'type'   => array('column' => 'type', 'unique' => 0),
                        'status' => array('column' => 'status', 'unique' => 0),
                    ),
                ),
                'users'          => array(
                    'indexes' => array(
                        'active_flg' => array('column' => 'active_flg', 'unique' => 0),
                    ),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'badges'         => array(
                    'type'       => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
                ),
                'groups'         => array(
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
                ),
                'job_categories' => array(
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
                ),
                'notifications'  => array(
                    'unread_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '未読フラグ(通知を開いたらOff)'),
                ),
                'posts'          => array(
                    'type'          => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
                    'public_flg'    => array('type' => 'boolean', 'null' => false, 'default' => '1'),
                    'important_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                ),
                'team_members'   => array(
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '有効フラグ(Offの場合はチームにログイン不可。チームメンバーによる当該メンバーのチーム内のコンテンツへのアクセスは可能。当該メンバーへの如何なる発信は不可)'),
                    'admin_flg'  => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'チーム管理者フラグ(Onの場合はチーム設定が可能)'),
                ),
                'threads'        => array(
                    'type'   => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'スレッドタイプ(1:ゴール作成,2:Feedback)'),
                    'status' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'スレッドステータス(1:Open,2:Close)'),
                ),
                'users'          => array(
                    'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'アクティブフラグ(ユーザ認証済みの場合On)'),
                ),
            ),
            'drop_field'  => array(
                'badges'         => array('', 'indexes' => array('active_flg', 'type')),
                'groups'         => array('', 'indexes' => array('active_flg')),
                'job_categories' => array('', 'indexes' => array('active_flg')),
                'notifications'  => array('', 'indexes' => array('unread_flg')),
                'posts'          => array('', 'indexes' => array('type', 'public_flg', 'important_flg')),
                'team_members'   => array('', 'indexes' => array('active_flg', 'admin_flg')),
                'threads'        => array('', 'indexes' => array('type', 'status')),
                'users'          => array('', 'indexes' => array('active_flg')),
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
