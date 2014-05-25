<?php

class EditUsersFields extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = '';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field' => array(
                'badges'        => array(
                    'default_badge_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'デフォルトバッジID(デフォルトで用意されているバッジ)'),
                    'type'             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
                    'count'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '利用されたカウント数(バッジが利用されるとカウントアップ。チーム管理者がリセット可能)'),
                    'max_count'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '利用可能数(カウント数が利用可能数に達した場合、バッジを新たに付与する事ができなくなる。)'),
                ),
                'comments'      => array(
                    'comment_like_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'コメントいいね数(comment_likesテーブルにレコードが追加されたらカウントアップされる)'),
                    'comment_read_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'コメント読んだ数(comment_readsテーブルにレコードが追加されたらカウントアップされる)'),
                ),
                'images'        => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => '画像タイプ(1:ユーザ画像,2:ゴール画像,3:バッジ画像,4:投稿画像)'),
                ),
                'notifications' => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => 'タイプ(1:ゴール,2:投稿,3:etc ...)'),
                ),
                'oauth_tokens'  => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => 'プロバイダタイプ(1:FB,2:Google)'),
                ),
                'posts'         => array(
                    'type'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
                    'comment_count'   => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'コメント数(commentsテーブルにレコードが追加されたらカウントアップされる)'),
                    'post_like_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'いいね数(post_likesテーブルni'),
                    'post_read_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '読んだ数'),
                ),
                'teams'         => array(
                    'type'             => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => 'プランタイプ(1:フリー,2:プロ,3:etc ... )'),
                    'start_term_month' => array('type' => 'integer', 'null' => false, 'default' => '4', 'unsigned' => false, 'comment' => '期間の開始月(入力可能な値は1〜12)'),
                    'border_months'    => array('type' => 'integer', 'null' => false, 'default' => '6', 'unsigned' => false, 'comment' => '期間の月数(４半期なら3,半年なら6, 0を認めない)'),
                ),
                'threads'       => array(
                    'type'   => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => 'スレッドタイプ(1:ゴール作成,2:Feedback)'),
                    'status' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => 'スレッドステータス(1:Open,2:Close)'),
                ),
                'users'         => array(
                    'gender_type'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '性別(1:男,2:女)'),
                    'timezone'     => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'タイムゾーン(UTCを起点とした時差)'),
                    'romanize_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'ローマ字表記フラグ(Onの場合は自分の名前がアプリ内で英語表記になる)。local_first_name,local_last_nameが入力されていても、first_name,last_nameがつかわれる。'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'badges'        => array(
                    'default_badge_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'デフォルトバッジID(デフォルトで用意されているバッジ)'),
                    'type'             => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
                    'count'            => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '利用されたカウント数(バッジが利用されるとカウントアップ。チーム管理者がリセット可能)'),
                    'max_count'        => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '利用可能数(カウント数が利用可能数に達した場合、バッジを新たに付与する事ができなくなる。)'),
                ),
                'comments'      => array(
                    'comment_like_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'コメントいいね数(comment_likesテーブルにレコードが追加されたらカウントアップされる)'),
                    'comment_read_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'コメント読んだ数(comment_readsテーブルにレコードが追加されたらカウントアップされる)'),
                ),
                'images'        => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => '画像タイプ(1:ユーザ画像,2:ゴール画像,3:バッジ画像,4:投稿画像)'),
                ),
                'notifications' => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'タイプ(1:ゴール,2:投稿,3:etc ...)'),
                ),
                'oauth_tokens'  => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'プロバイダタイプ(1:FB,2:Google)'),
                ),
                'posts'         => array(
                    'type'            => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
                    'comment_count'   => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'コメント数(commentsテーブルにレコードが追加されたらカウントアップされる)'),
                    'post_like_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => 'いいね数(post_likesテーブルni'),
                    'post_read_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'comment' => '読んだ数'),
                ),
                'teams'         => array(
                    'type'             => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'プランタイプ(1:フリー,2:プロ,3:etc ... )'),
                    'start_term_month' => array('type' => 'integer', 'null' => false, 'default' => '4', 'comment' => '期間の開始月(入力可能な値は1〜12)'),
                    'border_months'    => array('type' => 'integer', 'null' => false, 'default' => '6', 'comment' => '期間の月数(４半期なら3,半年なら6, 0を認めない)'),
                ),
                'threads'       => array(
                    'type'   => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'スレッドタイプ(1:ゴール作成,2:Feedback)'),
                    'status' => array('type' => 'integer', 'null' => false, 'default' => '1', 'comment' => 'スレッドステータス(1:Open,2:Close)'),
                ),
                'users'         => array(
                    'timezone'     => array('type' => 'float', 'null' => true, 'default' => null, 'comment' => 'タイムゾーン(UTCを起点とした時差)'),
                    'romanize_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ローマ字表記フラグ(Onの場合は自分の名前がアプリ内で英語表記になる)'),
                    'gender_type'  => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '性別(1:男,2:女)'),
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
     */
    public function after($direction)
    {
        return true;
    }
}
