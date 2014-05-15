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
        'id'                 => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'チームID', 'charset' => 'utf8'),
        'name'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'チーム名', 'charset' => 'utf8'),
        'image_id'           => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームロゴ画像ID(hasOneでImageモデルに関連)', 'charset' => 'utf8'),
        'type'               => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'プランタイプ(1:フリー,2:プロ,3:etc ... )'),
        'domain_limited_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'ドメイン限定フラグ(ONの場合は、指定されたドメイン名のメアドを所有していないとチームにログインできない)'),
        'domain_name'        => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'ドメイン名', 'charset' => 'utf8'),
        'start_term_month'   => array('type' => 'integer', 'null' => false, 'default' => '4', 'comment' => '期間の開始月(入力可能な値は1〜12)'),
        'border_months'      => array('type' => 'integer', 'null' => false, 'default' => '6', 'comment' => '期間の月数(４半期なら3,半年なら6, 0を認めない)'),
        'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
        'deleted'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームを削除した日付時刻'),
        'created'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームを追加した日付時刻'),
        'modified'           => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームを更新した日付時刻'),
        'indexes'            => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
            'id'                 => '53746f19-e6b8-4976-a445-0d9cac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 1,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 1,
            'border_months'      => 1,
            'del_flg'            => 1,
            'deleted'            => '2014-05-15 16:39:05',
            'created'            => '2014-05-15 16:39:05',
            'modified'           => '2014-05-15 16:39:05'
        ),
    );

}
