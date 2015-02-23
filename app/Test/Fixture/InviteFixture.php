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
        'id'                  => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '招待ID'),
        'from_user_id'        => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '招待元ユーザID(belongsToでUserモデルに関連)'),
        'to_user_id'          => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '招待先ユーザID(belongsToでUserモデルに関連)'),
        'team_id'             => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'email'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
        'message'             => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '招待メッセージ', 'charset' => 'utf8'),
        'email_verified'      => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'メアド認証判定('),
        'email_token'         => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
        'email_token_expires' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メアドトークン認証期限(メアド未認証でこの期限が切れた場合は再度、トークン発行)'),
        'type'                => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 3, 'unsigned' => true, 'comment' => '招待タイプ(0:通常招待,1:一括登録)'),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '招待を削除した日付時刻'),
        'created'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '招待を追加した日付時刻'),
        'modified'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '招待を更新した日付時刻'),
        'indexes'             => array(
            'PRIMARY'      => array('column' => 'id', 'unique' => 1),
            'from_user_id' => array('column' => 'from_user_id', 'unique' => 0),
            'to_user_id'   => array('column' => 'to_user_id', 'unique' => 0),
            'team_id'      => array('column' => 'team_id', 'unique' => 0),
            'email'        => array('column' => 'email', 'unique' => 0),
            'email_token'  => array('column' => 'email_token', 'unique' => 0),
            'del_flg'      => array('column' => 'del_flg', 'unique' => 0)
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
            'id'                  => '1',
            'from_user_id'        => '1234567890',
            'to_user_id'          => '1234567891',
            'team_id'             => '1234567892',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => false,
            'email_token'         => 'token_test001',
            'email_token_expires' => 1400725683,
            'del_flg'             => false,
            'deleted'             => null,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '2',
            'from_user_id'        => '1',
            'to_user_id'          => '2',
            'team_id'             => '1',
            'email'               => 'test@ppppp.com',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => false,
            'email_token'         => 'token_test002',
            'email_token_expires' => 1747880883,
            'del_flg'             => false,
            'deleted'             => null,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '3',
            'from_user_id'        => '1234567893',
            'to_user_id'          => null,
            'team_id'             => 'team_id_001',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => false,
            'email_token'         => 'token_not_user_001',
            'email_token_expires' => 1747880883,
            'del_flg'             => false,
            'deleted'             => null,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '4',
            'from_user_id'        => '1',
            'to_user_id'          => null,
            'team_id'             => '1',
            'email'               => 'test@ppppp.com',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => false,
            'email_token'         => 'token_test003',
            'email_token_expires' => 1747880883,
            'del_flg'             => false,
            'deleted'             => null,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '5',
            'from_user_id'        => '1',
            'to_user_id'          => null,
            'team_id'             => '1',
            'email'               => 'from@email.com',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => false,
            'email_token'         => 'token_test004',
            'email_token_expires' => 1747880883,
            'del_flg'             => false,
            'deleted'             => null,
            'created'             => 1400725683,
            'modified'            => 1400725683
        ),
        array(
            'id'                  => '',
            'from_user_id'        => '',
            'to_user_id'          => '',
            'team_id'             => '',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 6,
            'del_flg'             => 1,
            'deleted'             => 6,
            'created'             => 6,
            'modified'            => 6
        ),
        array(
            'id'                  => '',
            'from_user_id'        => '',
            'to_user_id'          => '',
            'team_id'             => '',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 7,
            'del_flg'             => 1,
            'deleted'             => 7,
            'created'             => 7,
            'modified'            => 7
        ),
        array(
            'id'                  => '',
            'from_user_id'        => '',
            'to_user_id'          => '',
            'team_id'             => '',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 8,
            'del_flg'             => 1,
            'deleted'             => 8,
            'created'             => 8,
            'modified'            => 8
        ),
        array(
            'id'                  => '',
            'from_user_id'        => '',
            'to_user_id'          => '',
            'team_id'             => '',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 9,
            'del_flg'             => 1,
            'deleted'             => 9,
            'created'             => 9,
            'modified'            => 9
        ),
        array(
            'id'                  => '',
            'from_user_id'        => '',
            'to_user_id'          => '',
            'team_id'             => '',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => 10,
            'del_flg'             => 1,
            'deleted'             => 10,
            'created'             => 10,
            'modified'            => 10
        ),
    );

}
