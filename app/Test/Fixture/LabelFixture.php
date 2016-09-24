<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * LabelFixture
 */
class LabelFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'               => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ),
        'team_id'          => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ),
        'name'             => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 128,
            'key'     => 'index',
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'ラベル',
            'charset' => 'utf8mb4'
        ),
        'goal_label_count' => array('type'     => 'integer',
                                    'null'     => false,
                                    'default'  => '0',
                                    'unsigned' => true,
                                    'comment'  => 'ゴールラベルのカウンタキャッシュ'
        ),
        'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'          => array('type'     => 'integer',
                                    'null'     => true,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'comment'  => '部署を削除した日付時刻'
        ),
        'created'          => array('type'     => 'integer',
                                    'null'     => true,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'comment'  => '部署を追加した日付時刻'
        ),
        'modified'         => array('type'     => 'integer',
                                    'null'     => true,
                                    'default'  => null,
                                    'unsigned' => true,
                                    'comment'  => '部署を更新した日付時刻'
        ),
        'indexes'          => array(
            'PRIMARY'             => array('column' => 'id', 'unique' => 1),
            'unique_name_team_id' => array('column' => array('name', 'team_id'), 'unique' => 1),
            'team_id'             => array('column' => 'team_id', 'unique' => 0)
        ),
        'tableParameters'  => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
