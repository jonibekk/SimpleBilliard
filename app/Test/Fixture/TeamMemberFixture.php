<?php

/**
 * TeamMemberFixture

 */
class TeamMemberFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                    => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'チームメンバーID'),
        'user_id'               => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
        'team_id'               => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'coach_user_id'         => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'コーチのユーザID(belongsToでUserモデルに関連)'),
        'member_no'             => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'メンバーナンバー(組織内でメンバーを識別する為のナンバー。exp社員番号)', 'charset' => 'utf8'),
        'group_id'              => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '部署ID(belongsToでgroupモデルに関連)'),
        'job_category_id'       => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '職種ID(belongsToでJobCategoryモデルに関連)'),
        'active_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => '有効フラグ(Offの場合はチームにログイン不可。チームメンバーによる当該メンバーのチーム内のコンテンツへのアクセスは可能。当該メンバーへの如何なる発信は不可)'),
        'invitation_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '招待中フラグ(招待済みで非アクティブユーザの管理用途)'),
        'evaluation_enable_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価対象フラグ(Offの場合は評価が不可能。対象ページへのアクセスおよび、一切の評価のアクションが行えない。)'),
        'admin_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => 'チーム管理者フラグ(Onの場合はチーム設定が可能)'),
        'last_login'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チーム最終ログイン日時'),
        'del_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'               => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームから外れた日付時刻'),
        'created'               => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームに参加した日付時刻'),
        'modified'              => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームメンバー設定を更新した日付時刻'),
        'indexes'               => array(
            'PRIMARY'         => array('column' => 'id', 'unique' => 1),
            'user_id'         => array('column' => 'user_id', 'unique' => 0),
            'team_id'         => array('column' => 'team_id', 'unique' => 0),
            'coach_user_id'   => array('column' => 'coach_user_id', 'unique' => 0),
            'group_id'        => array('column' => 'group_id', 'unique' => 0),
            'job_category_id' => array('column' => 'job_category_id', 'unique' => 0),
            'del_flg'         => array('column' => 'del_flg', 'unique' => 0),
            'active_flg'      => array('column' => 'active_flg', 'unique' => 0),
            'admin_flg'       => array('column' => 'admin_flg', 'unique' => 0)
        ),
        'tableParameters'       => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                    => '1',
            'user_id'               => '1',
            'team_id'               => '1',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg'               => false,
            'deleted'               => null,
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
        array(
            'id'         => '2',
            'user_id'    => '2',
            'team_id'    => '1',
            'coach_user_id'         => '',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => '',
            'job_category_id'       => '',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login' => '2014-05-22 02:28:04',
            'del_flg'    => false,
            'deleted'    => null,
            'created'    => '2014-05-22 02:28:04',
            'modified'   => '2014-05-22 02:28:04'
        ),
        array(
            'id'         => '3',
            'user_id'    => '12',
            'team_id'    => '1',
            'coach_user_id'         => '',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => '',
            'job_category_id'       => '',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login' => '2014-05-22 02:28:04',
            'del_flg'    => false,
            'deleted'    => null,
            'created'    => '2014-05-22 02:28:04',
            'modified'   => '2014-05-22 02:28:04'
        ),
        array(
            'id'         => '4',
            'user_id'    => '13',
            'team_id'    => '1',
            'coach_user_id'         => '',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => '',
            'job_category_id'       => '',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login' => '2014-05-22 02:28:04',
            'del_flg'    => false,
            'deleted'    => null,
            'created'    => '2014-05-22 02:28:04',
            'modified'   => '2014-05-22 02:28:04'
        ),
        array(
            'id'                    => '',
            'user_id'               => '',
            'team_id'               => '',
            'coach_user_id'         => '',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => '',
            'job_category_id'       => '',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => 5,
            'del_flg'               => 1,
            'deleted'               => 5,
            'created'               => 5,
            'modified'              => 5
        ),
        array(
            'id'                    => '',
            'user_id'               => '',
            'team_id'               => '',
            'coach_user_id'         => '',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => '',
            'job_category_id'       => '',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => 6,
            'del_flg'               => 1,
            'deleted'               => 6,
            'created'               => 6,
            'modified'              => 6
        ),
        array(
            'id'                    => '',
            'user_id'               => '',
            'team_id'               => '',
            'coach_user_id'         => '',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => '',
            'job_category_id'       => '',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => 7,
            'del_flg'               => 1,
            'deleted'               => 7,
            'created'               => 7,
            'modified'              => 7
        ),
        array(
            'id'                    => '',
            'user_id'               => '',
            'team_id'               => '',
            'coach_user_id'         => '',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => '',
            'job_category_id'       => '',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => 8,
            'del_flg'               => 1,
            'deleted'               => 8,
            'created'               => 8,
            'modified'              => 8
        ),
        array(
            'id'                    => '',
            'user_id'               => '',
            'team_id'               => '',
            'coach_user_id'         => '',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => '',
            'job_category_id'       => '',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => 9,
            'del_flg'               => 1,
            'deleted'               => 9,
            'created'               => 9,
            'modified'              => 9
        ),
        array(
            'id'                    => '',
            'user_id'               => '',
            'team_id'               => '',
            'coach_user_id'         => '',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => '',
            'job_category_id'       => '',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => 10,
            'del_flg'               => 1,
            'deleted'               => 10,
            'created'               => 10,
            'modified'              => 10
        ),
    );

}
