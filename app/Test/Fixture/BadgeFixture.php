<?php

/**
 * BadgeFixture

 */
class BadgeFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'バッジID', 'charset' => 'utf8'),
        'user_id'          => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ作成ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
        'team_id'          => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'name'             => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ名', 'charset' => 'utf8'),
        'description'      => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ詳細', 'charset' => 'utf8'),
        'image_id'         => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ画像ID(hasOneでImageモデルに関連)', 'charset' => 'utf8'),
        'default_badge_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'デフォルトバッジID(デフォルトで用意されているバッジ)'),
        'type'             => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
        'active_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
        'count'            => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '利用されたカウント数(バッジが利用されるとカウントアップ。チーム管理者がリセット可能)'),
        'max_count'        => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '利用可能数(カウント数が利用可能数に達した場合、バッジを新たに付与する事ができなくなる。)'),
        'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
        'deleted'          => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'バッジを削除した日付時刻'),
        'created'          => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'バッジを追加した日付時刻'),
        'modified'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'バッジを更新した日付時刻'),
        'indexes'          => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters'  => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'               => '53746f12-3660-4bda-a1ae-0d9cac11b50b',
            'user_id'          => 'Lorem ipsum dolor sit amet',
            'team_id'          => 'Lorem ipsum dolor sit amet',
            'name'             => 'Lorem ipsum dolor sit amet',
            'description'      => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'image_id'         => 'Lorem ipsum dolor sit amet',
            'default_badge_id' => 1,
            'type'             => 1,
            'active_flg'       => 1,
            'count'            => 1,
            'max_count'        => 1,
            'del_flg'          => 1,
            'deleted'          => '2014-05-15 16:38:58',
            'created'          => '2014-05-15 16:38:58',
            'modified'         => '2014-05-15 16:38:58'
        ),
    );

}
