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
        'email_verified'      => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'メアド認証判定('),
        'email_token'         => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
        'email_token_expires' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドトークン認証期限(メアド未認証でこの期限が切れた場合は再度、トークン発行)'),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
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
            'id'                  => '53746f15-0308-41d1-b69a-0d9cac11b50b',
            'from_user_id'        => 'Lorem ipsum dolor sit amet',
            'to_user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'             => 'Lorem ipsum dolor sit amet',
            'email'               => 'Lorem ipsum dolor sit amet',
            'message'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'email_verified'      => 1,
            'email_token'         => 'Lorem ipsum dolor sit amet',
            'email_token_expires' => '2014-05-15 16:39:01',
            'del_flg'             => 1,
            'deleted'             => '2014-05-15 16:39:01',
            'created'             => '2014-05-15 16:39:01',
            'modified'            => '2014-05-15 16:39:01'
        ),
    );

}
