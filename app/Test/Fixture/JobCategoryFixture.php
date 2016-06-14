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
        'id'              => array('type' => 'primary_key', 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '職種ID'),
        'team_id'         => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '職種名', 'charset' => 'utf8'),
        'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '職種の説明', 'charset' => 'utf8'),
        'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'key' => 'index', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '職種を削除した日付時刻'),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '職種を追加した日付時刻'),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '職種を更新した日付時刻'),
        'indexes'         => array(
            'PRIMARY'    => array('column' => 'id', 'unique' => 1),
            'team_id'    => array('column' => 'team_id', 'unique' => 0),
            'del_flg'    => array('column' => 'del_flg', 'unique' => 0),
            'active_flg' => array('column' => 'active_flg', 'unique' => 0)
        ),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array();

}
