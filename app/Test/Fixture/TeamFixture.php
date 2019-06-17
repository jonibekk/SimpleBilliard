<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * TeamFixture
 */
class TeamFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                           => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'チームID'
        ),
        'name'                         => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 128,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'チーム名',
            'charset' => 'utf8mb4'
        ),
        'photo_file_name'              => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'チームロゴ画像',
            'charset' => 'utf8mb4'
        ),
        'type'                         => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '1',
            'length'   => 3,
            'unsigned' => true,
            'comment'  => 'プランタイプ(1:フリー,2:プロ,3:etc ... )'
        ),
        'domain_limited_flg'           => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => 'ドメイン限定フラグ(ONの場合は、指定されたドメイン名のメアドを所有していないとチームにログインできない)'
        ),
        'domain_name'                  => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'length'  => 128,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'ドメイン名',
            'charset' => 'utf8mb4'
        ),
        'start_term_month'             => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '4',
            'length'   => 3,
            'unsigned' => true,
            'comment'  => '期間の開始月(入力可能な値は1〜12)'
        ),
        'border_months'                => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '6',
            'length'   => 3,
            'unsigned' => true,
            'comment'  => '期間の月数(４半期なら3,半年なら6, 0を認めない)'
        ),
        'timezone'                     => array(
            'type'     => 'float',
            'null'     => true,
            'default'  => null,
            'unsigned' => false,
            'comment'  => 'チームのタイムゾーン'
        ),
        'service_use_status'           => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'サービス利用ステータス(0: free trial,1: payed,2: read only,3: service expired,4: manual delete,5: auto delete)'
        ),
        'country'                      => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'length'  => 2,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '国コード',
            'charset' => 'utf8mb4'
        ),
        'default_translation_language' => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'length'  => 10,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Default translation language for the team',
            'charset' => 'utf8mb4'
        ),
        'service_use_state_start_date' => array(
            'type'    => 'date',
            'null'    => false,
            'default' => null,
            'comment' => '各ステートの開始日'
        ),
        'service_use_state_end_date'   => array(
            'type'    => 'date',
            'null'    => true,
            'default' => null,
            'comment' => '各ステートの終了日'
        ),
        'pre_register_amount_per_user' => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'length'   => 10,
            'unsigned' => true,
            'comment'  => 'Amount per user before registering payment plan'
        ),
        'del_flg'                      => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => '削除フラグ'
        ),
        'deleted'                      => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'チームを削除した日付時刻'
        ),
        'created'                      => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'チームを追加した日付時刻'
        ),
        'modified'                     => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'チームを更新した日付時刻'
        ),
        'indexes'                      => array(
            'PRIMARY'            => array('column' => 'id', 'unique' => 1),
            'service_use_status' => array('column' => 'service_use_status', 'unique' => 0)
        ),
        'tableParameters'              => array(
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'engine'  => 'InnoDB'
        )
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                           => '1',
            'name'                         => 'Lorem ipsum dolor sit amet',
            'photo_file_name'              => null,
            'type'                         => 1,
            'domain_limited_flg'           => 1,
            'domain_name'                  => 'Lorem ipsum dolor sit amet',
            'start_term_month'             => 1,
            'border_months'                => 1,
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 'JP',
            'default_translation_language' => null,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-01-01',
            'del_flg'                      => false,
            'deleted'                      => null,
            'created'                      => '2014-05-22 02:28:04',
            'modified'                     => '2014-05-22 02:28:04'
        ),
        array(
            'id'                           => '2',
            'name'                         => 'Lorem ipsum dolor sit amet',
            'photo_file_name'              => 'Lorem ipsum dolor sit amet',
            'type'                         => 2,
            'domain_limited_flg'           => 1,
            'domain_name'                  => 'Lorem ipsum dolor sit amet',
            'start_term_month'             => 2,
            'border_months'                => 2,
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 'JP',
            'default_translation_language' => null,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-01-01',
            'del_flg'                      => false,
            'deleted'                      => null,
            'created'                      => '2014-05-22 02:28:04',
            'modified'                     => '2014-05-22 02:28:04'
        ),
        array(
            'id'                           => '3',
            'name'                         => 'Lorem ipsum dolor sit amet',
            'photo_file_name'              => 'Lorem ipsum dolor sit amet',
            'type'                         => 3,
            'domain_limited_flg'           => 1,
            'domain_name'                  => 'Lorem ipsum dolor sit amet',
            'start_term_month'             => 3,
            'border_months'                => 3,
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 'JP',
            'default_translation_language' => null,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-01-01',
            'del_flg'                      => false,
            'deleted'                      => null,
            'created'                      => '2014-05-22 02:28:04',
            'modified'                     => '2014-05-22 02:28:04'
        ),
        array(
            'id'                           => '4',
            'name'                         => 'Lorem ipsum dolor sit amet',
            'photo_file_name'              => 'Lorem ipsum dolor sit amet',
            'type'                         => 4,
            'domain_limited_flg'           => 1,
            'domain_name'                  => 'Lorem ipsum dolor sit amet',
            'start_term_month'             => 4,
            'border_months'                => 4,
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 'JP',
            'default_translation_language' => null,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-01-01',
            'del_flg'                      => false,
            'deleted'                      => null,
            'created'                      => '2014-05-22 02:28:04',
            'modified'                     => '2014-05-22 02:28:04'
        ),
        array(
            'id'                           => '5',
            'name'                         => 'Lorem ipsum dolor sit amet',
            'photo_file_name'              => 'Lorem ipsum dolor sit amet',
            'type'                         => 5,
            'domain_limited_flg'           => 1,
            'domain_name'                  => 'Lorem ipsum dolor sit amet',
            'start_term_month'             => 5,
            'border_months'                => 5,
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 'JP',
            'default_translation_language' => null,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-01-01',
            'del_flg'                      => false,
            'deleted'                      => null,
            'created'                      => '2014-05-22 02:28:04',
            'modified'                     => '2014-05-22 02:28:04'
        ),
        array(
            'id'                           => '6',
            'name'                         => 'Lorem ipsum dolor sit amet',
            'photo_file_name'              => 'Lorem ipsum dolor sit amet',
            'type'                         => 6,
            'domain_limited_flg'           => 1,
            'domain_name'                  => 'Lorem ipsum dolor sit amet',
            'start_term_month'             => 6,
            'border_months'                => 6,
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 'JP',
            'default_translation_language' => null,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-01-01',
            'del_flg'                      => 1,
            'deleted'                      => 6,
            'created'                      => 6,
            'modified'                     => 6
        ),
        array(
            'id'                           => '7',
            'name'                         => 'Lorem ipsum dolor sit amet',
            'photo_file_name'              => 'Lorem ipsum dolor sit amet',
            'type'                         => 7,
            'domain_limited_flg'           => 1,
            'domain_name'                  => 'Lorem ipsum dolor sit amet',
            'start_term_month'             => 7,
            'border_months'                => 7,
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 'JP',
            'default_translation_language' => null,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-01-01',
            'del_flg'                      => 1,
            'deleted'                      => 7,
            'created'                      => 7,
            'modified'                     => 7
        ),
        array(
            'id'                           => '8',
            'name'                         => 'Lorem ipsum dolor sit amet',
            'photo_file_name'              => 'Lorem ipsum dolor sit amet',
            'type'                         => 8,
            'domain_limited_flg'           => 1,
            'domain_name'                  => 'Lorem ipsum dolor sit amet',
            'start_term_month'             => 8,
            'border_months'                => 8,
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 'JP',
            'default_translation_language' => null,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-01-01',
            'del_flg'                      => 1,
            'deleted'                      => 8,
            'created'                      => 8,
            'modified'                     => 8
        ),
        array(
            'id'                           => '9',
            'name'                         => 'Lorem ipsum dolor sit amet',
            'photo_file_name'              => 'Lorem ipsum dolor sit amet',
            'type'                         => 9,
            'domain_limited_flg'           => 1,
            'domain_name'                  => 'Lorem ipsum dolor sit amet',
            'start_term_month'             => 9,
            'border_months'                => 9,
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 'JP',
            'default_translation_language' => null,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-01-01',
            'del_flg'                      => 1,
            'deleted'                      => 9,
            'created'                      => 9,
            'modified'                     => 9
        ),
        array(
            'id'                           => '10',
            'name'                         => 'Lorem ipsum dolor sit amet',
            'photo_file_name'              => 'Lorem ipsum dolor sit amet',
            'type'                         => 10,
            'domain_limited_flg'           => 1,
            'domain_name'                  => 'Lorem ipsum dolor sit amet',
            'start_term_month'             => 10,
            'border_months'                => 10,
            'timezone'                     => 9,
            'service_use_status'           => 1,
            'country'                      => 'JP',
            'default_translation_language' => null,
            'service_use_state_start_date' => '2017-07-20',
            'service_use_state_end_date'   => '2020-01-01',
            'del_flg'                      => 1,
            'deleted'                      => 10,
            'created'                      => 10,
            'modified'                     => 10
        ),
    );

}
