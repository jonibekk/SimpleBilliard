<?php

/**
 * TeamFixture

 */
class TeamFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                 => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'チームID'),
        'name'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'チーム名', 'charset' => 'utf8'),
        'photo_file_name'    => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'チームロゴ画像', 'charset' => 'utf8'),
        'type'               => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'プランタイプ(1:フリー,2:プロ,3:etc ... )'),
        'domain_limited_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'ドメイン限定フラグ(ONの場合は、指定されたドメイン名のメアドを所有していないとチームにログインできない)'),
        'domain_name'        => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'ドメイン名', 'charset' => 'utf8'),
        'start_term_month'   => array('type' => 'integer', 'null' => false, 'default' => '4', 'length' => 3, 'unsigned' => true, 'comment' => '期間の開始月(入力可能な値は1〜12)'),
        'border_months'      => array('type' => 'integer', 'null' => false, 'default' => '6', 'length' => 3, 'unsigned' => true, 'comment' => '期間の月数(４半期なら3,半年なら6, 0を認めない)'),
        'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームを削除した日付時刻'),
        'created'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームを追加した日付時刻'),
        'modified'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームを更新した日付時刻'),
        'indexes'            => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'del_flg' => array('column' => 'del_flg', 'unique' => 0)
        ),
        'tableParameters'    => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                 => '1',
            'name'               => 'Lorem ipsum dolor sit amet',
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'type'               => 1,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 1,
            'border_months'      => 1,
            'del_flg'            => false,
            'deleted'            => null,
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '2',
            'name'               => 'Lorem ipsum dolor sit amet',
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'type'               => 2,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 2,
            'border_months'      => 2,
            'del_flg'            => false,
            'deleted'            => null,
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '3',
            'name'               => 'Lorem ipsum dolor sit amet',
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'type'               => 3,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 3,
            'border_months'      => 3,
            'del_flg'            => false,
            'deleted'            => null,
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '4',
            'name'               => 'Lorem ipsum dolor sit amet',
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'type'               => 4,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 4,
            'border_months'      => 4,
            'del_flg'            => false,
            'deleted'            => null,
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '5',
            'name'               => 'Lorem ipsum dolor sit amet',
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'type'               => 5,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 5,
            'border_months'      => 5,
            'del_flg'            => false,
            'deleted'            => null,
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '',
            'name'               => 'Lorem ipsum dolor sit amet',
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'type'               => 6,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 6,
            'border_months'      => 6,
            'del_flg'            => 1,
            'deleted'            => 6,
            'created'            => 6,
            'modified'           => 6
        ),
        array(
            'id'                 => '',
            'name'               => 'Lorem ipsum dolor sit amet',
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'type'               => 7,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 7,
            'border_months'      => 7,
            'del_flg'            => 1,
            'deleted'            => 7,
            'created'            => 7,
            'modified'           => 7
        ),
        array(
            'id'                 => '',
            'name'               => 'Lorem ipsum dolor sit amet',
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'type'               => 8,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 8,
            'border_months'      => 8,
            'del_flg'            => 1,
            'deleted'            => 8,
            'created'            => 8,
            'modified'           => 8
        ),
        array(
            'id'                 => '',
            'name'               => 'Lorem ipsum dolor sit amet',
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'type'               => 9,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 9,
            'border_months'      => 9,
            'del_flg'            => 1,
            'deleted'            => 9,
            'created'            => 9,
            'modified'           => 9
        ),
        array(
            'id'                 => '',
            'name'               => 'Lorem ipsum dolor sit amet',
            'photo_file_name'    => 'Lorem ipsum dolor sit amet',
            'type'               => 10,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 10,
            'border_months'      => 10,
            'del_flg'            => 1,
            'deleted'            => 10,
            'created'            => 10,
            'modified'           => 10
        ),
    );

}
