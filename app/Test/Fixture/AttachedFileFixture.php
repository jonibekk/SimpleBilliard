<?php

/**
 * AttachedFileFixture
 */
class AttachedFileFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                    => array('type' => 'primary_key', 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'user_id'               => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
        'team_id'               => array('type' => 'biginteger', 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'attached_file_name'    => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル名', 'charset' => 'utf8'),
        'file_type'             => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ファイルタイプ(0:画像,1:ビデオ,2:ドキュメント)'),
        'file_ext'              => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル拡張子', 'charset' => 'utf8'),
        'file_size'             => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ファイルのバイト数'),
        'model_type'            => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'モデルタイプ(0:Post,1:Comment)'),
        'display_file_list_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ファイル一覧に表示するフラグ'),
        'removable_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '削除可能フラグ'),
        'del_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
        'deleted'               => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'               => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
        'modified'              => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
        'indexes'               => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'post_id' => array('column' => 'user_id', 'unique' => 0),
            'team_id' => array('column' => 'team_id', 'unique' => 0)
        ),
        'tableParameters'       => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        [
            'id'                    => 1,
            'user_id'               => 1,
            'team_id'               => 1,
            'attached_file_name'    => 'test_file.txt',
            'file_type'             => 2,
            'file_ext'              => '.txt',
            'file_size'             => 100,
            'model_type'            => 0,
            'display_file_list_flg' => 1,
            'removable_flg'         => 1,
            'del_flg'               => 0,
            'deleted'               => null,
            'created'               => 1,
            'modified'              => 1,
        ],
        [
            'id'                    => 2,
            'user_id'               => 1,
            'team_id'               => 1,
            'attached_file_name'    => 'test_file_action.txt',
            'file_type'             => 2,
            'file_ext'              => '.txt',
            'file_size'             => 100,
            'model_type'            => 2,
            'display_file_list_flg' => 1,
            'removable_flg'         => 1,
            'del_flg'               => 0,
            'deleted'               => null,
            'created'               => 1,
            'modified'              => 1,
        ],
        [
            'id'                    => 3,
            'user_id'               => 1,
            'team_id'               => 1,
            'attached_file_name'    => 'test_file_comment.txt',
            'file_type'             => 2,
            'file_ext'              => '.txt',
            'file_size'             => 100,
            'model_type'            => 1,
            'display_file_list_flg' => 1,
            'removable_flg'         => 1,
            'del_flg'               => 0,
            'deleted'               => null,
            'created'               => 1,
            'modified'              => 1,
        ],
        [
            'id'                    => 4,
            'user_id'               => 1,
            'team_id'               => 1,
            'attached_file_name'    => 'test_file_action_comment.txt',
            'file_type'             => 2,
            'file_ext'              => '.txt',
            'file_size'             => 100,
            'model_type'            => 1,
            'display_file_list_flg' => 1,
            'removable_flg'         => 1,
            'del_flg'               => 0,
            'deleted'               => null,
            'created'               => 1,
            'modified'              => 1,
        ],
        [
            'id'                    => 5,
            'user_id'               => 1,
            'team_id'               => 1,
            'attached_file_name'    => 'test_file_public_circle.txt',
            'file_type'             => 2,
            'file_ext'              => '.txt',
            'file_size'             => 100,
            'model_type'            => 0,
            'display_file_list_flg' => 1,
            'removable_flg'         => 1,
            'del_flg'               => 0,
            'deleted'               => null,
            'created'               => 1,
            'modified'              => 1,
        ],
        [
            'id'                    => 6,
            'user_id'               => 2,
            'team_id'               => 1,
            'attached_file_name'    => 'test_file_for_user.txt',
            'file_type'             => 2,
            'file_ext'              => '.txt',
            'file_size'             => 100,
            'model_type'            => 0,
            'display_file_list_flg' => 1,
            'removable_flg'         => 1,
            'del_flg'               => 0,
            'deleted'               => null,
            'created'               => 1,
            'modified'              => 1,
        ],
        [
            'id'                    => 7,
            'user_id'               => 2,
            'team_id'               => 1,
            'attached_file_name'    => 'test_file_for_secret_circle.txt',
            'file_type'             => 2,
            'file_ext'              => '.txt',
            'file_size'             => 100,
            'model_type'            => 0,
            'display_file_list_flg' => 1,
            'removable_flg'         => 1,
            'del_flg'               => 0,
            'deleted'               => null,
            'created'               => 1,
            'modified'              => 1,
        ],
        [
            'id'                    => 8,
            'user_id'               => 2,
            'team_id'               => 1,
            'attached_file_name'    => 'test_file_for_self.txt',
            'file_type'             => 2,
            'file_ext'              => '.txt',
            'file_size'             => 100,
            'model_type'            => 0,
            'display_file_list_flg' => 1,
            'removable_flg'         => 1,
            'del_flg'               => 0,
            'deleted'               => null,
            'created'               => 1,
            'modified'              => 1,
        ],
    );

}
