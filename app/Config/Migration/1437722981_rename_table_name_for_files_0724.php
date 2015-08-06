<?php

class RenameTableNameForFiles0724 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'rename_table_name_for_files_0724';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'attached_files' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'file_name'       => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル名', 'charset' => 'utf8'),
                    'file_type'       => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ファイルタイプ(0:画像,1:ビデオ,2:ドキュメント)'),
                    'file_ext'        => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル拡張子', 'charset' => 'utf8'),
                    'file_size'       => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ファイルのバイト数'),
                    'model_type'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'モデルタイプ(0:Post,1:Comment)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'post_id' => array('column' => 'user_id', 'unique' => 0),
                        'team_id' => array('column' => 'team_id', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
            'create_field' => array(
                'comment_files' => array(
                    'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)', 'after' => 'comment_id'),
                    'indexes'          => array(
                        'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0),
                    ),
                ),
                'post_files'    => array(
                    'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)', 'after' => 'post_id'),
                    'indexes'          => array(
                        'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0),
                    ),
                ),
            ),
            'drop_field'   => array(
                'comment_files' => array('file_id', 'indexes' => array('file_id')),
                'post_files'    => array('file_id', 'indexes' => array('file_id')),
            ),
            'drop_table'   => array(
                'files'
            ),
        ),
        'down' => array(
            'drop_table'   => array(
                'attached_files'
            ),
            'drop_field'   => array(
                'comment_files' => array('attached_file_id', 'indexes' => array('attached_file_id')),
                'post_files'    => array('attached_file_id', 'indexes' => array('attached_file_id')),
            ),
            'create_field' => array(
                'comment_files' => array(
                    'file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)'),
                    'indexes' => array(
                        'file_id' => array('column' => 'file_id', 'unique' => 0),
                    ),
                ),
                'post_files'    => array(
                    'file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)'),
                    'indexes' => array(
                        'file_id' => array('column' => 'file_id', 'unique' => 0),
                    ),
                ),
            ),
            'create_table' => array(
                'files' => array(
                    'id'              => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
                    'user_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
                    'team_id'         => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'file_name'       => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル名', 'charset' => 'utf8'),
                    'file_type'       => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ファイルタイプ(0:画像,1:ビデオ,2:ドキュメント)'),
                    'file_ext'        => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル拡張子', 'charset' => 'utf8'),
                    'file_size'       => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ファイルのバイト数'),
                    'model_type'      => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'モデルタイプ(0:Post,1:Comment)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
                    'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'post_id' => array('column' => 'user_id', 'unique' => 0),
                        'team_id' => array('column' => 'team_id', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        return true;
    }
}
