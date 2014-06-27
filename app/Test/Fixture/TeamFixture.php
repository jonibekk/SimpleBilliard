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
        'type'               => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => 'プランタイプ(1:フリー,2:プロ,3:etc ... )'),
        'domain_limited_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'ドメイン限定フラグ(ONの場合は、指定されたドメイン名のメアドを所有していないとチームにログインできない)'),
        'domain_name'        => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'ドメイン名', 'charset' => 'utf8'),
        'start_term_month'   => array('type' => 'integer', 'null' => false, 'default' => '4', 'unsigned' => false, 'comment' => '期間の開始月(入力可能な値は1〜12)'),
        'border_months'      => array('type' => 'integer', 'null' => false, 'default' => '6', 'unsigned' => false, 'comment' => '期間の月数(４半期なら3,半年なら6, 0を認めない)'),
        'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
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
            'id'                 => '537ce224-c21c-41b6-a808-433dac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 1,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 1,
            'border_months'      => 1,
            'del_flg' => false,
            'deleted' => null,
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '537ce224-e79c-4b3e-b0cd-433dac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 2,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 2,
            'border_months'      => 2,
            'del_flg' => false,
            'deleted' => null,
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '537ce224-0358-41c1-a593-433dac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 3,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 3,
            'border_months'      => 3,
            'del_flg' => false,
            'deleted' => null,
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '537ce224-1f14-4d4f-9a15-433dac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 4,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 4,
            'border_months'      => 4,
            'del_flg' => false,
            'deleted' => null,
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '537ce224-3b34-48bb-9c08-433dac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 5,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 5,
            'border_months'      => 5,
            'del_flg' => false,
            'deleted' => null,
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '537ce224-5754-4b6c-900d-433dac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 6,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 6,
            'border_months'      => 6,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:04',
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '537ce224-7374-43cb-a2fd-433dac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 7,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 7,
            'border_months'      => 7,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:04',
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '537ce224-8f30-4395-a3d6-433dac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 8,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 8,
            'border_months'      => 8,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:04',
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '537ce224-aa88-4557-927a-433dac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 9,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 9,
            'border_months'      => 9,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:04',
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
        array(
            'id'                 => '537ce224-c70c-484e-9f1e-433dac11b50b',
            'name'               => 'Lorem ipsum dolor sit amet',
            'image_id'           => 'Lorem ipsum dolor sit amet',
            'type'               => 10,
            'domain_limited_flg' => 1,
            'domain_name'        => 'Lorem ipsum dolor sit amet',
            'start_term_month'   => 10,
            'border_months'      => 10,
            'del_flg'            => 1,
            'deleted'            => '2014-05-22 02:28:04',
            'created'            => '2014-05-22 02:28:04',
            'modified'           => '2014-05-22 02:28:04'
        ),
    );

}
