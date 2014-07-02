<?php

/**
 * InviteFixture

 */
class InviteFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                  => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '招待ID', 'charset' => 'utf8'),
        'from_user_id'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '招待元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'to_user_id'          => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '招待先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'             => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'email'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
        'message'             => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '招待メッセージ', 'charset' => 'utf8'),
        'email_verified'      => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'メアド認証判定('),
        'email_token'         => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
        'email_token_expires' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドトークン認証期限(メアド未認証でこの期限が切れた場合は再度、トークン発行)'),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'             => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '招待を削除した日付時刻'),
        'created'             => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '招待を追加した日付時刻'),
        'modified'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '招待を更新した日付時刻'),
        'indexes'             => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters'     => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                  => '537ce223-29d0-431b-bfe4-433dac11b50b',
            'from_user_id'   => 'aaa',
            'to_user_id'     => 'bbb',
            'team_id'        => 'ccc',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified' => false,
            'email_token'    => 'token_test001',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'        => false,
            'deleted'        => null,
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-507c-442a-a361-433dac11b50b',
            'from_user_id'        => '537ce224-8c0c-4c99-be76-433dac11b50b',
            'to_user_id'          => '537ce224-c708-4084-b879-433dac11b50b',
            'team_id'             => '537ce224-c21c-41b6-a808-433dac11b50b',
            'email'               => 'test@ppppp.com',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => false,
            'email_token'         => 'token_test002',
            'email_token_expires' => '2025-05-22 02:28:03',
            'del_flg'             => false,
            'deleted'             => null,
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-6dc8-4f5e-86f1-433dac11b50b',
            'from_user_id'        => 'Lorem ipsum dolor sit amet',
            'to_user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-8a4c-4f17-8b7c-433dac11b50b',
            'from_user_id'        => 'Lorem ipsum dolor sit amet',
            'to_user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-a6d0-42b1-8bd3-433dac11b50b',
            'from_user_id'        => 'Lorem ipsum dolor sit amet',
            'to_user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-c2f0-48ce-bda7-433dac11b50b',
            'from_user_id'        => 'Lorem ipsum dolor sit amet',
            'to_user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-df74-44e4-9f14-433dac11b50b',
            'from_user_id'        => 'Lorem ipsum dolor sit amet',
            'to_user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-fbf8-4abe-bb7e-433dac11b50b',
            'from_user_id'        => 'Lorem ipsum dolor sit amet',
            'to_user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-1368-4209-b6a4-433dac11b50b',
            'from_user_id'        => 'Lorem ipsum dolor sit amet',
            'to_user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
        array(
            'id'                  => '537ce223-30b4-4498-8e71-433dac11b50b',
            'from_user_id'        => 'Lorem ipsum dolor sit amet',
            'to_user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-22 02:28:03',
            'del_flg'             => 1,
            'deleted'             => '2014-05-22 02:28:03',
            'created'             => '2014-05-22 02:28:03',
            'modified'            => '2014-05-22 02:28:03'
        ),
    );

}
