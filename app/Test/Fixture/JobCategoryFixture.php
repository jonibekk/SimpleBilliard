<?php

/**
 * JobCategoryFixture

 */
class JobCategoryFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '職種ID', 'charset' => 'utf8'),
        'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '職種名', 'charset' => 'utf8'),
        'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '職種の説明', 'charset' => 'utf8'),
        'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '職種を削除した日付時刻'),
        'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '職種を追加した日付時刻'),
        'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '職種を更新した日付時刻'),
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
            'id'          => '537ce223-6fb0-442d-9174-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-9c9c-4171-baae-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-b6c8-4ad7-8366-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-d02c-4ab1-96dc-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-e92c-467f-8901-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-022c-4896-9377-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-1b2c-4d3c-8822-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-33c8-47af-b654-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-4cc8-48dd-bcd9-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
            'name'        => 'Lorem ipsum dolor sit amet',
            'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'active_flg'  => 1,
            'del_flg'     => 1,
            'deleted'     => '2014-05-22 02:28:03',
            'created'     => '2014-05-22 02:28:03',
            'modified'    => '2014-05-22 02:28:03'
        ),
        array(
            'id'          => '537ce223-662c-4a57-91cb-433dac11b50b',
            'team_id'     => 'Lorem ipsum dolor sit amet',
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
