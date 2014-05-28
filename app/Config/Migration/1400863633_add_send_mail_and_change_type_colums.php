<?php

class AddSendMailAndChangeTypeColums extends CakeMigration
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
            'create_field' => array(
                'badges' => array(
                    'default_badge_no' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'デフォルトバッジNo(デフォルトで用意されているバッジ)', 'after' => 'image_id'),
                ),
            ),
            'drop_field'   => array(
                'badges' => array('default_badge_id',),
            ),
            'alter_field'  => array(
                'badges'        => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
                ),
                'images'        => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => '画像タイプ(1:ユーザ画像,2:ゴール画像,3:バッジ画像,4:投稿画像)'),
                ),
                'notifications' => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'タイプ(1:ゴール,2:投稿,3:etc ...)'),
                ),
                'oauth_tokens'  => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'プロバイダタイプ(1:FB,2:Google)'),
                ),
                'posts'         => array(
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
                ),
                'teams'         => array(
                    'type'             => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'プランタイプ(1:フリー,2:プロ,3:etc ... )'),
                    'start_term_month' => array('type' => 'integer', 'null' => false, 'default' => '4', 'length' => 3, 'unsigned' => true, 'comment' => '期間の開始月(入力可能な値は1〜12)'),
                    'border_months'    => array('type' => 'integer', 'null' => false, 'default' => '6', 'length' => 3, 'unsigned' => true, 'comment' => '期間の月数(４半期なら3,半年なら6, 0を認めない)'),
                ),
                'threads'       => array(
                    'type'   => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'スレッドタイプ(1:ゴール作成,2:Feedback)'),
                    'status' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'スレッドステータス(1:Open,2:Close)'),
                ),
                'users'         => array(
                    'gender_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => '性別(1:男,2:女)'),
                ),
            ),
            'create_table' => array(
                'send_mails' => array(
                    'id'              => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'メール送信ID', 'charset' => 'utf8'),
                    'from_user_id'    => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'to_user_id'      => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
                    'team_id'         => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
                    'template_type'   => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 3, 'unsigned' => true, 'comment' => 'メールテンプレタイプ'),
                    'item'            => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アイテム(JSONエンコード)', 'charset' => 'utf8'),
                    'sent_datetime'   => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メール送信を実行した日付時刻'),
                    'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メール送信を削除した日付時刻'),
                    'created'         => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メール送信を追加した日付時刻'),
                    'modified'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'メール送信を更新した日付時刻'),
                    'indexes'         => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                    ),
                    'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'badges' => array('default_badge_no',),
            ),
            'create_field' => array(
                'badges' => array(
                    'default_badge_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'デフォルトバッジID(デフォルトで用意されているバッジ)'),
                ),
            ),
            'alter_field'  => array(
                'badges'        => array(
                    'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'バッジタイプ(1:賞賛,2:スキル)'),
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
                    'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '投稿タイプ(1:Nomal,2:バッジ,3:ゴール作成,4:etc ... )'),
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
                    'gender_type' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '性別(1:男,2:女)'),
                ),
            ),
            'drop_table'   => array(
                'send_mails'
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
