<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * ApprovalHistoryFixture
 */
class ApprovalHistoryFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id'                      => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ],
        'collaborator_id'         => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'コラボレーターID(hasManyでcollaboratorモデルに関連)'
        ],
        'user_id'                 => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'ユーザーID(belongsToでUserモデルに関連)'
        ],
        'comment'                 => [
            'type'    => 'text',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'コメント',
            'charset' => 'utf8mb4'
        ],
        'action_status'           => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => ' 状態(0 = アクションなし,1 =コメントのみ, 2 = 評価対象にする, 3 = 評価対象にしない, 4 =修正依頼)'
        ],
        'select_clear_status'     => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => '0:no select, 1:is clear, 2:is not clear'
        ],
        'select_important_status' => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => false,
            'comment'  => '0:no select, 1:is important, 2:not important'
        ],
        'del_flg'                 => [
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => '削除フラグ'
        ],
        'deleted'                 => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ],
        'created'                 => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => '追加した日付時刻'
        ],
        'modified'                => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '更新した日付時刻'
        ],
        'indexes'                 => [
            'PRIMARY'         => ['column' => 'id', 'unique' => 1],
            'collaborator_id' => ['column' => 'collaborator_id', 'unique' => 0],
            'created'         => ['column' => 'created', 'unique' => 0]
        ],
        'tableParameters'         => [
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'engine'  => 'InnoDB'
        ]
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id'                  => '1',
            'collaborator_id'     => '',
            'user_id'             => '',
            'comment'             => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'action_status'       => 1,
            'select_clear_status'     => 0,
            'select_important_status' => 0,
            'del_flg'             => 1,
            'deleted'             => 1,
            'created'             => 1,
            'modified'            => 1
        ],
    ];

}
