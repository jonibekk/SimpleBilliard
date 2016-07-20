<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * DeviceFixture
 */
class DeviceFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ),
        'user_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'UserID(belongsToでUserモデルに関連)'
        ),
        'device_token'    => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'アプリインストール毎に発行される識別子',
            'charset' => 'utf8mb4'
        ),
        'os_type'         => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => '0:ios 1:android 99:other'
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ),
        'created'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '登録した日付時刻'
        ),
        'modified'        => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '最後に更新した日付時刻'
        ),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'user_id' => array('column' => 'user_id', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id'           => '1',
            'user_id'      => '1',
            'device_token' => 'ios_dummy1',
            'os_type'      => '0',
            'del_flg'      => false,
        ],
        [
            'id'           => '2',
            'user_id'      => '2',
            'device_token' => 'android_dummy1',
            'os_type'      => '1',
            'del_flg'      => false,
        ],
        [
            'id'           => '3',
            'user_id'      => '3',
            'device_token' => 'ios_dummy2',
            'os_type'      => '0',
            'del_flg'      => false,
        ],
        [
            'id'           => '4',
            'user_id'      => '3',
            'device_token' => 'android_dummy2',
            'os_type'      => '1',
            'del_flg'      => false,
        ],
        [
            'id'           => '5',
            'user_id'      => '4',
            'device_token' => 'android_dummy3',
            'os_type'      => '1',
            'del_flg'      => true,
        ],
    ];

}
