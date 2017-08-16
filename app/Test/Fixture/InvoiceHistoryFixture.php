<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * InvoiceHistory Fixture
 */
class InvoiceHistoryFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id'                => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ],
        'team_id'           => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'チームID(belongsToでTeamモデルに関連)'
        ],
        'order_date'        => [
            'type'    => 'date',
            'null'    => false,
            'default' => null,
            'key'     => 'index',
            'comment' => '注文登録時のUTC日付'
        ],
        'system_order_code' => [
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => '後払い.comから返される注文ID',
            'charset' => 'utf8mb4'
        ],
        'order_status'      => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => '後払い.comから返される与信状況。与信OK:1、与信NG:2、与信中:0 '
        ],
        'del_flg'           => ['type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'],
        'deleted'           => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ],
        'created'           => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '追加した日付時刻'
        ],
        'modified'          => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '更新した日付時刻'
        ],
        'indexes'           => [
            'PRIMARY'    => ['column' => 'id', 'unique' => 1],
            'team_id'    => ['column' => 'team_id', 'unique' => 0],
            'order_date' => ['column' => 'order_date', 'unique' => 0]
        ],
        'tableParameters'   => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
