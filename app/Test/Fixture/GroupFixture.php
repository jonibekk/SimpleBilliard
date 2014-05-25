<?php

/**
 * GroupFixture

 */
class GroupFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '部署ID', 'charset' => 'utf8'),
        'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'parent_id'       => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '上位部署ID(belongsToで同モデルに関連)', 'charset' => 'utf8'),
        'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '部署名', 'charset' => 'utf8'),
        'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '部署の説明', 'charset' => 'utf8'),
        'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '部署を削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '部署を追加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '部署を更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
            'id'          => '537ce223-8c88-4e92-a087-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'parent_id'   => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-af4c-4306-b530-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'parent_id'   => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-c914-4eb0-aa01-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'parent_id'   => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-e214-442e-bdae-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'parent_id'   => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-fb14-4cc7-95bc-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'parent_id'   => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-13b0-417c-97cf-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'parent_id'   => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-292c-4f93-b7b5-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'parent_id'   => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-43bc-4fb9-9046-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'parent_id'   => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-5cbc-4c6d-8033-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'parent_id'   => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-7b34-42e3-9883-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'parent_id'   => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
    );

}
