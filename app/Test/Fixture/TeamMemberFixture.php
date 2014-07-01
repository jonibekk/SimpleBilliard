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
        'id'                    => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'チームメンバーID', 'charset' => 'utf8'),
        'user_id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'coach_user_id'         => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コーチのユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'member_no'             => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'メンバーナンバー(組織内でメンバーを識別する為のナンバー。exp社員番号)', 'charset' => 'utf8'),
        'group_id'              => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '部署ID(belongsToでgroupモデルに関連)', 'charset' => 'utf8'),
        'job_category_id'       => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '職種ID(belongsToでJobCategoryモデルに関連)', 'charset' => 'utf8'),
        'active_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '有効フラグ(Offの場合はチームにログイン不可。チームメンバーによる当該メンバーのチーム内のコンテンツへのアクセスは可能。当該メンバーへの如何なる発信は不可)'),
        'invitation_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '招待中フラグ(招待済みで非アクティブユーザの管理用途)'),
        'evaluation_enable_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価対象フラグ(Offの場合は評価が不可能。対象ページへのアクセスおよび、一切の評価のアクションが行えない。)'),
        'admin_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'チーム管理者フラグ(Onの場合はチーム設定が可能)'),
        'last_login'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チーム最終ログイン日時'),
        'del_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'               => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームから外れた日付時刻'),
        'created'               => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームに参加した日付時刻'),
        'modified'              => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームメンバー設定を更新した日付時刻'),
        'indexes'               => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
            'id'                    => '537ce224-b7c4-4d12-81c8-433dac11b50b',
            'user_id' => '537ce224-8c0c-4c99-be76-433dac11b50b',
            'team_id' => '537ce224-c21c-41b6-a808-433dac11b50b',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg' => false,
            'deleted' => null,
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
        array(
            'id'                    => '537ce224-e2bc-4fd1-9b98-433dac11b50b',
            'user_id'               => 'Lorem ipsum dolor sit amet',
            'team_id'               => 'Lorem ipsum dolor sit amet',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg'               => 1,
            'deleted'               => '2014-05-22 02:28:04',
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
        array(
            'id'                    => '537ce224-03f0-4a93-bf38-433dac11b50b',
            'user_id'               => 'Lorem ipsum dolor sit amet',
            'team_id'               => 'Lorem ipsum dolor sit amet',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg'               => 1,
            'deleted'               => '2014-05-22 02:28:04',
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
        array(
            'id'                    => '537ce224-2524-4b98-b5bc-433dac11b50b',
            'user_id'               => 'Lorem ipsum dolor sit amet',
            'team_id'               => 'Lorem ipsum dolor sit amet',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg'               => 1,
            'deleted'               => '2014-05-22 02:28:04',
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
        array(
            'id'                    => '537ce224-4658-4045-976c-433dac11b50b',
            'user_id'               => 'Lorem ipsum dolor sit amet',
            'team_id'               => 'Lorem ipsum dolor sit amet',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg'               => 1,
            'deleted'               => '2014-05-22 02:28:04',
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
        array(
            'id'                    => '537ce224-678c-4eb7-961d-433dac11b50b',
            'user_id'               => 'Lorem ipsum dolor sit amet',
            'team_id'               => 'Lorem ipsum dolor sit amet',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg'               => 1,
            'deleted'               => '2014-05-22 02:28:04',
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
        array(
            'id'                    => '537ce224-88c0-447f-9ef2-433dac11b50b',
            'user_id'               => 'Lorem ipsum dolor sit amet',
            'team_id'               => 'Lorem ipsum dolor sit amet',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg'               => 1,
            'deleted'               => '2014-05-22 02:28:04',
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
        array(
            'id'                    => '537ce224-a92c-4c2a-9108-433dac11b50b',
            'user_id'               => 'Lorem ipsum dolor sit amet',
            'team_id'               => 'Lorem ipsum dolor sit amet',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg'               => 1,
            'deleted'               => '2014-05-22 02:28:04',
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
        array(
            'id'                    => '537ce224-c9fc-4314-9b27-433dac11b50b',
            'user_id'               => 'Lorem ipsum dolor sit amet',
            'team_id'               => 'Lorem ipsum dolor sit amet',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg'               => 1,
            'deleted'               => '2014-05-22 02:28:04',
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
        array(
            'id'                    => '537ce224-ea04-45f3-99a7-433dac11b50b',
            'user_id'               => 'Lorem ipsum dolor sit amet',
            'team_id'               => 'Lorem ipsum dolor sit amet',
            'coach_user_id'         => 'Lorem ipsum dolor sit amet',
            'member_no'             => 'Lorem ipsum dolor sit amet',
            'group_id'              => 'Lorem ipsum dolor sit amet',
            'job_category_id'       => 'Lorem ipsum dolor sit amet',
            'active_flg'            => 1,
            'invitation_flg'        => 1,
            'evaluation_enable_flg' => 1,
            'admin_flg'             => 1,
            'last_login'            => '2014-05-22 02:28:04',
            'del_flg'               => 1,
            'deleted'               => '2014-05-22 02:28:04',
            'created'               => '2014-05-22 02:28:04',
            'modified'              => '2014-05-22 02:28:04'
        ),
    );

}
