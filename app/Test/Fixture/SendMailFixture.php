<?php

/**
 * SendMailFixture

 */
class SendMailFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'メール送信ID'),
        'from_user_id'    => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)'),
        'team_id'         => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'notification_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '通知ID(belongsToでNotificationモデルに関連)'),
        'template_type'   => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'メールテンプレタイプ'),
        'item'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アイテム(JSONエンコード)', 'charset' => 'utf8'),
        'sent_datetime'   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を実行した日付時刻'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'         => array('column' => 'id', 'unique' => 1),
            'from_user_id'    => array('column' => 'from_user_id', 'unique' => 0),
            'team_id'         => array('column' => 'team_id', 'unique' => 0),
            'del_flg'         => array('column' => 'del_flg', 'unique' => 0),
            'notification_id' => array('column' => 'notification_id', 'unique' => 0)
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
            'id'              => '',
            'from_user_id'    => '',
            'team_id'         => '',
            'notification_id' => '',
            'template_type'   => 1,
            'item'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'sent_datetime'   => 1,
            'del_flg'         => 1,
            'deleted'         => 1,
            'created'         => 1,
            'modified'        => 1
        ),
        array(
            'id'              => '',
            'from_user_id'    => '',
            'team_id'         => '',
            'notification_id' => '',
            'template_type'   => 2,
            'item'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'sent_datetime'   => 2,
            'del_flg'         => 1,
            'deleted'         => 2,
            'created'         => 2,
            'modified'        => 2
        ),
        array(
            'id'              => '',
            'from_user_id'    => '',
            'team_id'         => '',
            'notification_id' => '',
            'template_type'   => 3,
            'item'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'sent_datetime'   => 3,
            'del_flg'         => 1,
            'deleted'         => 3,
            'created'         => 3,
            'modified'        => 3
        ),
        array(
            'id'              => '',
            'from_user_id'    => '',
            'team_id'         => '',
            'notification_id' => '',
            'template_type'   => 4,
            'item'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'sent_datetime'   => 4,
            'del_flg'         => 1,
            'deleted'         => 4,
            'created'         => 4,
            'modified'        => 4
        ),
        array(
            'id'              => '',
            'from_user_id'    => '',
            'team_id'         => '',
            'notification_id' => '',
            'template_type'   => 5,
            'item'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'sent_datetime'   => 5,
            'del_flg'         => 1,
            'deleted'         => 5,
            'created'         => 5,
            'modified'        => 5
        ),
        array(
            'id'              => '',
            'from_user_id'    => '',
            'team_id'         => '',
            'notification_id' => '',
            'template_type'   => 6,
            'item'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'sent_datetime'   => 6,
            'del_flg'         => 1,
            'deleted'         => 6,
            'created'         => 6,
            'modified'        => 6
        ),
        array(
            'id'              => '',
            'from_user_id'    => '',
            'team_id'         => '',
            'notification_id' => '',
            'template_type'   => 7,
            'item'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'sent_datetime'   => 7,
            'del_flg'         => 1,
            'deleted'         => 7,
            'created'         => 7,
            'modified'        => 7
        ),
        array(
            'id'              => '',
            'from_user_id'    => '',
            'team_id'         => '',
            'notification_id' => '',
            'template_type'   => 8,
            'item'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'sent_datetime'   => 8,
            'del_flg'         => 1,
            'deleted'         => 8,
            'created'         => 8,
            'modified'        => 8
        ),
        array(
            'id'              => '',
            'from_user_id'    => '',
            'team_id'         => '',
            'notification_id' => '',
            'template_type'   => 9,
            'item'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'sent_datetime'   => 9,
            'del_flg'         => 1,
            'deleted'         => 9,
            'created'         => 9,
            'modified'        => 9
        ),
        array(
            'id'              => '',
            'from_user_id'    => '',
            'team_id'         => '',
            'notification_id' => '',
            'template_type'   => 10,
            'item'            => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'sent_datetime'   => 10,
            'del_flg'         => 1,
            'deleted'         => 10,
            'created'         => 10,
            'modified'        => 10
        ),
    );

}
