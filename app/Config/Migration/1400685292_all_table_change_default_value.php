<?php

class AllTableChangeDefaultValue extends CakeMigration
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
                'badges'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'comment_likes'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'comment_mentions' => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'comment_reads'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'comments'         => array(
                    'comment_like_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメントいいね数(comment_likesテーブルにレコードが追加されたらカウントアップされる)'),
                    'comment_read_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメント読んだ数(comment_readsテーブルにレコードが追加されたらカウントアップされる)'),
                    'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'emails'           => array(
                    'email_verified' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'メアド認証判定('),
                    'del_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'given_badges'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'groups'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'images'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'invites'          => array(
                    'email_verified' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'メアド認証判定('),
                    'del_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'job_categories'   => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'messages'         => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'notifications'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'oauth_tokens'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'post_likes'       => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'post_mentions'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'post_reads'       => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'posts'            => array(
                    'comment_count'   => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'コメント数(commentsテーブルにレコードが追加されたらカウントアップされる)'),
                    'post_like_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'いいね数(post_likesテーブルni'),
                    'post_read_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '読んだ数'),
                    'important_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'posts_images'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'team_members'     => array(
                    'invitation_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '招待中フラグ(招待済みで非アクティブユーザの管理用途)'),
                    'admin_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'チーム管理者フラグ(Onの場合はチーム設定が可能)'),
                    'del_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'teams'            => array(
                    'domain_limited_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'ドメイン限定フラグ(ONの場合は、指定されたドメイン名のメアドを所有していないとチームにログインできない)'),
                    'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'threads'          => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'users'            => array(
                    'hide_year_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '誕生日の年を隠すフラグ'),
                    'no_pass_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'パスワード未使用フラグ(ソーシャルログインのみ利用時)'),
                    'active_flg'    => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'アクティブフラグ(ユーザ認証済みの場合On)'),
                    'admin_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '管理者フラグ(管理画面が開ける人)'),
                    'romanize_flg'  => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'ローマ字表記フラグ(Onの場合は自分の名前がアプリ内で英語表記になる)。local_first_name,local_last_nameが入力されていても、first_name,last_nameがつかわれる。'),
                    'del_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'badges'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'comment_likes'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'comment_mentions' => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'comment_reads'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'comments'         => array(
                    'comment_like_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'コメントいいね数(comment_likesテーブルにレコードが追加されたらカウントアップされる)'),
                    'comment_read_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'コメント読んだ数(comment_readsテーブルにレコードが追加されたらカウントアップされる)'),
                    'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'emails'           => array(
                    'email_verified' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'メアド認証判定('),
                    'del_flg'        => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'given_badges'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'groups'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'images'           => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'invites'          => array(
                    'email_verified' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'メアド認証判定('),
                    'del_flg'        => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'job_categories'   => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'messages'         => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'notifications'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'oauth_tokens'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'post_likes'       => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'post_mentions'    => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'post_reads'       => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'posts'            => array(
                    'comment_count'   => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'コメント数(commentsテーブルにレコードが追加されたらカウントアップされる)'),
                    'post_like_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'いいね数(post_likesテーブルni'),
                    'post_read_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '読んだ数'),
                    'important_flg'   => array('type' => 'boolean', 'null' => false, 'default' => null),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'posts_images'     => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'team_members'     => array(
                    'invitation_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '招待中フラグ(招待済みで非アクティブユーザの管理用途)'),
                    'admin_flg'      => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'チーム管理者フラグ(Onの場合はチーム設定が可能)'),
                    'del_flg'        => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'teams'            => array(
                    'domain_limited_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'ドメイン限定フラグ(ONの場合は、指定されたドメイン名のメアドを所有していないとチームにログインできない)'),
                    'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'threads'          => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
                ),
                'users'            => array(
                    'hide_year_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '誕生日の年を隠すフラグ'),
                    'no_pass_flg'   => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'パスワード未使用フラグ(ソーシャルログインのみ利用時)'),
                    'active_flg'    => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'アクティブフラグ(ユーザ認証済みの場合On)'),
                    'admin_flg'     => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '管理者フラグ(管理画面が開ける人)'),
                    'romanize_flg'  => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'ローマ字表記フラグ(Onの場合は自分の名前がアプリ内で英語表記になる)。local_first_name,local_last_nameが入力されていても、first_name,last_nameがつかわれる。'),
                    'del_flg'       => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
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
