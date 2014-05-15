<?php

class AddBasicTables extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     * @access public
     */
    public $description = '';

    /**
     * Actions to be performed
     *
     * @var array $migration
     * @access public
     */
    public $migration = array(
        'up'   => array(
            'create_table' => array(
                'badges'           => array(
                    'id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'バッジID', 'charset' => 'utf8'),
                    'user_id'          => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ作成ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'          => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'name'             => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ名', 'charset' => 'utf8'),
                    'description'      => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ詳細', 'charset' => 'utf8'),
                    'image_id'         => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ画像ID(hasOneでImageモデルに関連)', 'charset' => 'utf8'),
                    'default_badge_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'デフォルトバッジID(デフォルトで用意されているバッジ)'),
                    'type'             => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
                    'active_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
                    'count'            => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '利用されたカウント数(バッジが利用されるとカウントアップ。チーム管理者がリセット可能)'),
                    'max_count'        => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '利用可能数(カウント数が利用可能数に達した場合、バッジを新たに付与する事ができなくなる。)'),
                    'del_flg'          => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'          => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'バッジを削除した日付時刻'),
                    'created'          => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'バッジを追加した日付時刻'),
                    'modified'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'バッジを更新した日付時刻'),
                    'indexes'          => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters'  => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'comment_likes'    => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'コメントいいねID', 'charset' => 'utf8'),
                    'comment_id'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コメントID(belongsToでcommentモデルに関連)', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'いいねしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'コメントを削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'コメントを追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'コメントを更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'comment_mentions' => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'コメントメンションID', 'charset' => 'utf8'),
                    'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'メンションユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'comment_readings' => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'コメント読んだID', 'charset' => 'utf8'),
                    'comment_id'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コメントID(belongsToでcommentモデルに関連)', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'コメントを削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'コメントを追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'コメントを更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'comments'         => array(
                    'id'                    => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'コメントID', 'charset' => 'utf8'),
                    'post_id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
                    'user_id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コメントしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'body'                  => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント本文', 'charset' => 'utf8'),
                    'comment_like_count'    => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'コメントいいね数(comment_likesテーブルにレコードが追加されたらカウントアップされる)'),
                    'comment_reading_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'コメント読んだ数(comment_readingsテーブルにレコードが追加されたらカウントアップされる)'),
                    'del_flg'               => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'               => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を削除した日付時刻'),
                    'created'               => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を追加した日付時刻'),
                    'modified'              => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を更新した日付時刻'),
                    'indexes'               => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters'       => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'divisions'        => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '部署ID', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'parent_id'       => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '上位部署ID(belongsToで同モデルに関連)', 'charset' => 'utf8'),
                    'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '部署名', 'charset' => 'utf8'),
                    'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '部署の説明', 'charset' => 'utf8'),
                    'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '部署を削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '部署を追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '部署を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'emails'           => array(
                    'id'                  => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'メアドID', 'charset' => 'utf8'),
                    'user_id'             => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'email'               => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
                    'email_verified'      => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'メアド認証判定('),
                    'email_token'         => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
                    'email_token_expires' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドトークン認証期限(メアド未認証でこの期限が切れた場合は再度、トークン発行)'),
                    'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'             => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを削除した日付時刻'),
                    'created'             => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを登録した日付時刻'),
                    'modified'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドを最後に更新した日付時刻'),
                    'indexes'             => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'email'   => array('column' => 'email', 'unique' => 0),
                    ),
                    'tableParameters'     => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'given_badges'     => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '所有バッジID', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジ所有ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'grant_user_id'   => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'バッジあげたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(hasOneでPostモデルに関連)', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '所有バッジを削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '所有バッジを追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '所有バッジを更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'images'           => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '画像ID', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => '画像タイプ(1:ユーザ画像,2:ゴール画像,3:バッジ画像,4:投稿画像)'),
                    'name'            => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像名', 'charset' => 'utf8'),
                    'item_file_name'  => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像ファイル名', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '画像を削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '画像を追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '画像を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'invites'          => array(
                    'id'                  => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '招待ID', 'charset' => 'utf8'),
                    'from_user_id'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '招待元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'to_user_id'          => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '招待先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'             => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'email'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
                    'message'             => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '招待メッセージ', 'charset' => 'utf8'),
                    'email_verified'      => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'メアド認証判定('),
                    'email_token'         => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
                    'email_token_expires' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メアドトークン認証期限(メアド未認証でこの期限が切れた場合は再度、トークン発行)'),
                    'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'             => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '招待を削除した日付時刻'),
                    'created'             => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '招待を追加した日付時刻'),
                    'modified'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '招待を更新した日付時刻'),
                    'indexes'             => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters'     => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'job_categories'   => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '職種ID', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '職種名', 'charset' => 'utf8'),
                    'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '職種の説明', 'charset' => 'utf8'),
                    'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '職種を削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '職種を追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '職種を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'messages'         => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'メッセージID', 'charset' => 'utf8'),
                    'from_user_id'    => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'to_user_id'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'thread_id'       => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'スレッドID(belongsToでThreadモデルに関連)', 'charset' => 'utf8'),
                    'body'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メッセージ本文', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メッセージを削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メッセージを追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メッセージを更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'notifications'    => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '通知ID', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'タイプ(1:ゴール,2:投稿,3:etc ...)'),
                    'from_user_id'    => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '通知元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'body'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '通知本文', 'charset' => 'utf8'),
                    'unread_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '未読フラグ(通知を開いたらOff)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '通知を削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '通知を追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '通知を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'oauth_tokens'     => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'OauthトークンID', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'プロバイダタイプ(1:FB,2:Google)'),
                    'uid'             => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'プロバイダー固有ID', 'charset' => 'utf8'),
                    'token'           => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'トークン', 'charset' => 'utf8'),
                    'expires'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'トークン認証期限(この期限が切れた場合は再度、トークン発行)'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ソーシャルログイン紐付け解除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ソーシャルログインを登録した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ソーシャルログインを最後に更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'uid'     => array('column' => 'uid', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'post_likes'       => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿いいねID', 'charset' => 'utf8'),
                    'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'いいねしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'post_mentions'    => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿メンションID', 'charset' => 'utf8'),
                    'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'メンションユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'post_readings'    => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿読んだID', 'charset' => 'utf8'),
                    'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'posts'            => array(
                    'id'                 => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿ID', 'charset' => 'utf8'),
                    'user_id'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿作成ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'body'               => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿本文', 'charset' => 'utf8'),
                    'type'               => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
                    'comment_count'      => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'コメント数(commentsテーブルにレコードが追加されたらカウントアップされる)'),
                    'post_like_count'    => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'いいね数(post_likesテーブルni'),
                    'post_reading_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => '読んだ数'),
                    'public_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '1'),
                    'important_flg'      => array('type' => 'boolean', 'null' => false, 'default' => null),
                    'goal_id'            => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
                    'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を削除した日付時刻'),
                    'created'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を追加した日付時刻'),
                    'modified'           => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '投稿を更新した日付時刻'),
                    'indexes'            => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters'    => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'posts_images'     => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿画像ID', 'charset' => 'utf8'),
                    'post_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルと関連)', 'charset' => 'utf8'),
                    'image_id'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '画像ID(belongsToでImageモデルと関連)', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '所有バッジを削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '所有バッジを追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '所有バッジを更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'team_members'     => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'チームメンバーID', 'charset' => 'utf8'),
                    'user_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'coach_user_id'   => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コーチのユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'division_id'     => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '部署ID(belongsToでDivisionモデルに関連)', 'charset' => 'utf8'),
                    'job_category_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '職種ID(belongsToでJobCategoryモデルに関連)', 'charset' => 'utf8'),
                    'active_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '有効フラグ(Offの場合はチームにログイン不可。チームメンバーによる当該メンバーのチーム内のコンテンツへのアクセスは可能。当該メンバーへの如何なる発信は不可)'),
                    'admin_flg'       => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'チーム管理者フラグ(Onの場合はチーム設定が可能)'),
                    'last_login'      => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チーム最終ログイン日時'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームから外れた日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームに参加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームメンバー設定を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'teams'            => array(
                    'id'                 => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'チームID', 'charset' => 'utf8'),
                    'name'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'チーム名', 'charset' => 'utf8'),
                    'image_id'           => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームロゴ画像ID(hasOneでImageモデルに関連)', 'charset' => 'utf8'),
                    'type'               => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'プランタイプ(1:フリー,2:プロ,3:etc ... )'),
                    'domain_limited_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'ドメイン限定フラグ(ONの場合は、指定されたドメイン名のメアドを所有していないとチームにログインできない)'),
                    'domain_name'        => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'ドメイン名', 'charset' => 'utf8'),
                    'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームを削除した日付時刻'),
                    'created'            => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームを追加した日付時刻'),
                    'modified'           => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'チームを更新した日付時刻'),
                    'indexes'            => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters'    => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
                'threads'          => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'スレッドID', 'charset' => 'utf8'),
                    'from_user_id'    => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'to_user_id'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'type'            => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'スレッドタイプ(1:ゴール作成,2:Feedback)'),
                    'status'          => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'スレッドステータス(1:Open,2:Close)'),
                    'name'            => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'スレッド名', 'charset' => 'utf8'),
                    'description'     => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'スレッドの詳細', 'charset' => 'utf8'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'スレッドを削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'スレッドを追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'スレッドを更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
            'create_field' => array(
                'users' => array(
                    'middle_name'       => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '英ミドルネーム', 'charset' => 'utf8', 'after' => 'last_name'),
                    'local_middle_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '母国のミドルネーム', 'charset' => 'utf8', 'after' => 'local_last_name'),
                    'gender_type'       => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '性別(1:男,2:女)', 'after' => 'local_middle_name'),
                    'birth_day'         => array('type' => 'date', 'null' => true, 'default' => null, 'comment' => '誕生日', 'after' => 'gender_type'),
                    'hide_year_flg'     => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '誕生日の年を隠すフラグ', 'after' => 'birth_day'),
                    'hometown'          => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '出身地', 'charset' => 'utf8', 'after' => 'hide_year_flg'),
                    'comment'           => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント', 'charset' => 'utf8', 'after' => 'hometown'),
                    'password_token'    => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'パスワードトークン(パスワード失念時の認証用)', 'charset' => 'utf8', 'after' => 'password'),
                    'no_pass_flg'       => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'パスワード未使用フラグ(ソーシャルログインのみ利用時)', 'after' => 'password_token'),
                    'profile_image_id'  => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'プロフィール画像ID(hasOneでImageモデルに関連)', 'charset' => 'utf8', 'after' => 'no_pass_flg'),
                    'primary_email_id'  => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'プライマリメールアドレスID(hasOneでEmailモデルに関連)', 'charset' => 'utf8', 'after' => 'profile_image_id'),
                    'active_flg'        => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'アクティブフラグ(ユーザ認証済みの場合On)', 'after' => 'primary_email_id'),
                    'last_login'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '最終ログイン日時', 'after' => 'active_flg'),
                    'admin_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '管理者フラグ(管理画面が開ける人)', 'after' => 'last_login'),
                    'default_team_id'   => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'デフォルトチーム(belongsToでTeamモデルに関連)', 'charset' => 'utf8', 'after' => 'admin_flg'),
                    'timezone'          => array('type' => 'float', 'null' => true, 'default' => null, 'comment' => 'タイムゾーン(UTCを起点とした時差)', 'after' => 'default_team_id'),
                    'auto_timezone_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自動タイムゾーンフラグ(Onの場合はOSからタイムゾーンを取得する)', 'after' => 'timezone'),
                    'language'          => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8', 'after' => 'auto_timezone_flg'),
                    'auto_language_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自動言語設定フラグ(Onの場合はブラウザから言語を取得する)', 'after' => 'language'),
                    'romanize_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ローマ字表記フラグ(Onの場合は自分の名前がアプリ内で英語表記になる)', 'after' => 'auto_language_flg'),
                    'update_email_flg'  => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '更新情報メールフラグ(Onの場合はアプリから更新情報がメールで届く)', 'after' => 'romanize_flg'),
                    'del_flg'           => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ', 'after' => 'update_email_flg'),
                    'deleted'           => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ユーザが退会した日付時刻', 'after' => 'del_flg'),
                ),
            ),
            'drop_field'   => array(
                'users' => array('email',),
            ),
            'alter_field'  => array(
                'users' => array(
                    'id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID', 'charset' => 'utf8'),
                    'local_first_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '母国の名', 'charset' => 'utf8'),
                    'local_last_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '母国の姓', 'charset' => 'utf8'),
                    'password'         => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'パスワード(暗号化)', 'charset' => 'utf8'),
                    'created'          => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ユーザーデータを登録した日付時刻'),
                    'modified'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ユーザーデータを最後に更新した日付時刻'),
                ),
            ),
        ),
        'down' => array(
            'drop_table'   => array(
                'badges', 'comment_likes', 'comment_mentions', 'comment_readings', 'comments', 'divisions', 'emails', 'given_badges', 'images', 'invites', 'job_categories', 'messages', 'notifications', 'oauth_tokens', 'post_likes', 'post_mentions', 'post_readings', 'posts', 'posts_images', 'team_members', 'teams', 'threads'
            ),
            'drop_field'   => array(
                'users' => array('middle_name', 'local_middle_name', 'gender_type', 'birth_day', 'hide_year_flg', 'hometown', 'comment', 'password_token', 'no_pass_flg', 'profile_image_id', 'primary_email_id', 'active_flg', 'last_login', 'admin_flg', 'default_team_id', 'timezone', 'auto_timezone_flg', 'language', 'auto_language_flg', 'romanize_flg', 'update_email_flg', 'del_flg', 'deleted',),
            ),
            'create_field' => array(
                'users' => array(
                    'email' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'Eメールアドレス', 'charset' => 'utf8'),
                ),
            ),
            'alter_field'  => array(
                'users' => array(
                    'id'               => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
                    'password'         => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
                    'local_first_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'ローカル名', 'charset' => 'utf8'),
                    'local_last_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'ローカル姓', 'charset' => 'utf8'),
                    'created'          => array('type' => 'datetime', 'null' => false, 'default' => null),
                    'modified'         => array('type' => 'datetime', 'null' => false, 'default' => null),
                ),
            ),
        ),
    );

    /**
     * Before migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     * @access public
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction , up or down direction of migration process
     *
     * @return boolean Should process continue
     * @access public
     */
    public function after($direction)
    {
        return true;
    }
}
