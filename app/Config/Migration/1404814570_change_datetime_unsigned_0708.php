<?php

class ChangeDatetimeUnsigned0708 extends CakeMigration
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
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'バッジを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'バッジを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'バッジを更新した日付時刻'),
                ),
                'comment_likes'    => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを更新した日付時刻'),
                ),
                'comment_mentions' => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
                ),
                'comment_reads'    => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'コメントを更新した日付時刻'),
                ),
                'comments'         => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
                ),
                'emails'           => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを登録した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを最後に更新した日付時刻'),
                ),
                'given_badges'     => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '所有バッジを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを更新した日付時刻'),
                ),
                'groups'           => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '部署を更新した日付時刻'),
                ),
                'images'           => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '画像を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '画像を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '画像を更新した日付時刻'),
                ),
                'images_posts'     => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '所有バッジを更新した日付時刻'),
                ),
                'invites'          => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '招待を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '招待を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '招待を更新した日付時刻'),
                ),
                'job_categories'   => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '職種を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '職種を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '職種を更新した日付時刻'),
                ),
                'local_names'      => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを登録した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メアドを最後に更新した日付時刻'),
                ),
                'messages'         => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メッセージを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メッセージを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メッセージを更新した日付時刻'),
                ),
                'notifications'    => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '通知を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '通知を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '通知を更新した日付時刻'),
                ),
                'oauth_tokens'     => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ソーシャルログイン紐付け解除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ソーシャルログインを登録した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ソーシャルログインを最後に更新した日付時刻'),
                ),
                'post_likes'       => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
                ),
                'post_mentions'    => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
                ),
                'post_reads'       => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を更新した日付時刻'),
                ),
                'posts'            => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
                ),
                'send_mails'       => array(
                    'sent_datetime' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を実行した日付時刻'),
                    'deleted'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を削除した日付時刻'),
                    'created'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を追加した日付時刻'),
                    'modified'      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'メール送信を更新した日付時刻'),
                ),
                'team_members'     => array(
                    'last_login' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チーム最終ログイン日時'),
                    'deleted'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームから外れた日付時刻'),
                    'created'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームに参加した日付時刻'),
                    'modified'   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームメンバー設定を更新した日付時刻'),
                ),
                'teams'            => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チームを更新した日付時刻'),
                ),
                'threads'          => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'スレッドを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドを更新した日付時刻'),
                ),
                'users'            => array(
                    'last_login' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '最終ログイン日時'),
                    'deleted'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザが退会した日付時刻'),
                    'created'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザーデータを登録した日付時刻'),
                    'modified'   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'ユーザーデータを最後に更新した日付時刻'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'badges'           => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'バッジを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'バッジを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'バッジを更新した日付時刻'),
                ),
                'comment_likes'    => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'コメントを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'コメントを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'コメントを更新した日付時刻'),
                ),
                'comment_mentions' => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を更新した日付時刻'),
                ),
                'comment_reads'    => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'コメントを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'コメントを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'コメントを更新した日付時刻'),
                ),
                'comments'         => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を更新した日付時刻'),
                ),
                'emails'           => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メアドを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メアドを登録した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メアドを最後に更新した日付時刻'),
                ),
                'given_badges'     => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '所有バッジを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '所有バッジを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '所有バッジを更新した日付時刻'),
                ),
                'groups'           => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '部署を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '部署を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '部署を更新した日付時刻'),
                ),
                'images'           => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '画像を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '画像を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '画像を更新した日付時刻'),
                ),
                'images_posts'     => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '所有バッジを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '所有バッジを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '所有バッジを更新した日付時刻'),
                ),
                'invites'          => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '招待を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '招待を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '招待を更新した日付時刻'),
                ),
                'job_categories'   => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '職種を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '職種を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '職種を更新した日付時刻'),
                ),
                'local_names'      => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メアドを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メアドを登録した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メアドを最後に更新した日付時刻'),
                ),
                'messages'         => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メッセージを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => 'メッセージを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メッセージを更新した日付時刻'),
                ),
                'notifications'    => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '通知を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '通知を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '通知を更新した日付時刻'),
                ),
                'oauth_tokens'     => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ソーシャルログイン紐付け解除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ソーシャルログインを登録した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ソーシャルログインを最後に更新した日付時刻'),
                ),
                'post_likes'       => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を更新した日付時刻'),
                ),
                'post_mentions'    => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を更新した日付時刻'),
                ),
                'post_reads'       => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を更新した日付時刻'),
                ),
                'posts'            => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
                ),
                'send_mails'       => array(
                    'sent_datetime' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メール送信を実行した日付時刻'),
                    'deleted'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メール送信を削除した日付時刻'),
                    'created'       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メール送信を追加した日付時刻'),
                    'modified'      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メール送信を更新した日付時刻'),
                ),
                'team_members'     => array(
                    'last_login' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チーム最終ログイン日時'),
                    'deleted'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チームから外れた日付時刻'),
                    'created'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チームに参加した日付時刻'),
                    'modified'   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チームメンバー設定を更新した日付時刻'),
                ),
                'teams'            => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チームを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チームを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チームを更新した日付時刻'),
                ),
                'threads'          => array(
                    'deleted'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'スレッドを削除した日付時刻'),
                    'created'  => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => 'スレッドを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => 'スレッドを更新した日付時刻'),
                ),
                'users'            => array(
                    'last_login' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '最終ログイン日時'),
                    'deleted'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ユーザが退会した日付時刻'),
                    'created'    => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ユーザーデータを登録した日付時刻'),
                    'modified'   => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ユーザーデータを最後に更新した日付時刻'),
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
