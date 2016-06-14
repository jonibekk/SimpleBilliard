<?php

/**
 * GroupVisionFixture
 */
class GroupVisionFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'primary_key', 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'name'            => array('type' => 'text', 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'グループビジョン名', 'charset' => 'utf8'),
        'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'グループビジョンの説明', 'charset' => 'utf8'),
        'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像', 'charset' => 'utf8'),
        'create_user_id'  => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ユーザID(belongsToでUserモデルに関連)'),
        'modify_user_id'  => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '最終編集者ユーザID(belongsToでUserモデルに関連)'),
        'team_id'         => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'group_id'        => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'グループID(belongsToでGroupモデルに関連)'),
        'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(0の場合はアーカイブ)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'        => array('column' => 'id', 'unique' => 1),
            'create_user_id' => array('column' => 'create_user_id', 'unique' => 0),
            'modify_user_id' => array('column' => 'modify_user_id', 'unique' => 0),
            'group_id'       => array('column' => 'group_id', 'unique' => 0),
            'team_id'        => array('column' => 'team_id', 'unique' => 0)
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
            'id'             => '1',
            'name'           => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'description'    => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'create_user_id' => '',
            'modify_user_id' => '',
            'team_id'        => '',
            'group_id'       => '',
            'active_flg'     => 1,
            'del_flg'        => 1,
            'deleted'        => 1,
            'created'        => 1,
            'modified'       => 1
        ),
    );

}
