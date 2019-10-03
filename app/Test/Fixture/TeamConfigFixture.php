<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * TeamConfigFixture
 */
class TeamConfigFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id'                           => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ],
        'team_id'                      => [
            'type'    => 'biginteger',
            'null'    => false,
            'default' => '0',
            'unsigned' => true,
            'comment' => ''
        ],
        'config'                       => [
            'type'    => 'text',
            'null'    => false,
            'default' => '{}',
            'comment' => ''
        ],
        'del_flg'                      => [
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => '削除フラグ'
        ],
        'deleted'                      => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'チームを削除した日付時刻'
        ],
        'created'                      => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'チームを追加した日付時刻'
        ],
        'modified'                     => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'チームを更新した日付時刻'
        ]
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [];

}
