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
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'チームメンバーID', 'charset' => 'utf8'),
        'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'coach_user_id'   => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コーチのユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'group_id'        => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '部署ID(belongsToでgroupモデルに関連)', 'charset' => 'utf8'),
        'job_category_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '職種ID(belongsToでJobCategoryモデルに関連)', 'charset' => 'utf8'),
        'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '有効フラグ(Offの場合はチームにログイン不可。チームメンバーによる当該メンバーのチーム内のコンテンツへのアクセスは可能。当該メンバーへの如何なる発信は不可)'),
        'admin_flg'       => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'チーム管理者フラグ(Onの場合はチーム設定が可能)'),
        'last_login'      => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チーム最終ログイン日時'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームから外れた日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームに参加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームメンバー設定を更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'              => '53746f18-5a94-44e9-b19b-0d9cac11b50b',
            'user_id'         => 'Lorem ipsum dolor sit amet',
            'team_id'         => 'Lorem ipsum dolor sit amet',
            'coach_user_id'   => 'Lorem ipsum dolor sit amet',
            'group_id'        => 'Lorem ipsum dolor sit amet',
            'job_category_id' => 'Lorem ipsum dolor sit amet',
            'active_flg'      => 1,
            'admin_flg'       => 1,
            'last_login'      => '2014-05-15 16:39:04',
            'del_flg'         => 1,
            'deleted'         => '2014-05-15 16:39:04',
            'created'         => '2014-05-15 16:39:04',
            'modified'        => '2014-05-15 16:39:04'
        ),
    );

}
