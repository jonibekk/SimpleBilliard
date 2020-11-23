<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * GroupFixture
 */
class GroupFixture extends CakeTestFixtureEx
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
            'comment'  => '部署ID'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'name'            => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 128,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '部署名',
            'charset' => 'utf8mb4'
        ),
        'description'     => array(
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '部署の説明',
            'charset' => 'utf8mb4'
        ),
        'active_flg'      => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'key'     => 'index',
            'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'
        ),
        'archived_flg'    => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'key'     => 'index',
            'comment' => 'flag indicating if a group is archived or not'
        ),
        'del_flg'         => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'key'     => 'index',
            'comment' => '削除フラグ'
        ),
        'deleted'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '部署を削除した日付時刻'
        ),
        'created'         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '部署を追加した日付時刻'
        ),
        'modified'        => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '部署を更新した日付時刻'
        ),
        'indexes'         => array(
            'PRIMARY'    => array('column' => 'id', 'unique' => 1),
            'team_id'    => array('column' => 'team_id', 'unique' => 0),
            'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
            'active_flg' => array('column' => 'active_flg', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'          => '1',
            'team_id'     => '1',
            'name'        => 'グループ1',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 0,
            'deleted'     => null,
            'created'     => 1,
            'modified'    => 1
        ),
        array(
            'id'          => '2',
            'team_id'     => '1',
            'name'        => 'グループ2',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 0,
            'deleted'     => null,
            'created'     => 1,
            'modified'    => 1
        ),
        array(
            'id'          => '3',
            'team_id'     => '1',
            'name'        => 'テストグループ',
            'description' => 'テストグループ',
            'active_flg'  => 1,
            'del_flg'     => 0,
            'deleted'     => 0,
            'created'     => 2,
            'modified'    => 2
        ),
        array(
            'id'          => '4',
            'team_id'     => '1',
            'name'        => 'first group',
            'description' => '',
            'active_flg'  => 1,
            'del_flg'     => 0,
            'deleted'     => 0,
            'created'     => 3,
            'modified'    => 3
        ),
        array(
            'id'          => '5',
            'team_id'     => '1',
            'name'        => 'first group 2',
            'description' => '',
            'active_flg'  => 1,
            'del_flg'     => 0,
            'deleted'     => 0,
            'created'     => 3,
            'modified'    => 3
        ),
        array(
            'id'          => '6',
            'team_id'     => '1',
            'name'        => 'first group 3',
            'description' => '',
            'active_flg'  => 1,
            'del_flg'     => 0,
            'deleted'     => 0,
            'created'     => 4,
            'modified'    => 4
        ),
        array(
            'id'          => '7',
            'team_id'     => '',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => 5,
            'created'     => 5,
            'modified'    => 5
        ),
        array(
            'id'          => '8',
            'team_id'     => '',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => 6,
            'created'     => 6,
            'modified'    => 6
        ),
        array(
            'id'          => '9',
            'team_id'     => '',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => 7,
            'created'     => 7,
            'modified'    => 7
        ),
        array(
            'id'          => '10',
            'team_id'     => '',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => 8,
            'created'     => 8,
            'modified'    => 8
        ),
        array(
            'id'          => '11',
            'team_id'     => '',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => 9,
            'created'     => 9,
            'modified'    => 9
        ),
        array(
            'id'          => '12',
            'team_id'     => '',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => 10,
            'created'     => 10,
            'modified'    => 10
        ),
    );

}
