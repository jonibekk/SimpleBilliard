<?php

class AddFieldsToNotifySettings0506 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_fields_to_notify_settings_0506';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'notify_settings' => array(
                    'feed_post_app_flg'                             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '閲覧可能な投稿があった際のアプリ通知', 'after' => 'user_id'),
                    'feed_post_email_flg'                           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '閲覧可能な投稿があった際のメール通知', 'after' => 'feed_post_app_flg'),
                    'feed_commented_on_my_post_app_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の投稿にコメントがあった際のアプリ通知', 'after' => 'feed_post_email_flg'),
                    'feed_commented_on_my_post_email_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の投稿にコメントがあった際のメール通知', 'after' => 'feed_commented_on_my_post_app_flg'),
                    'feed_commented_on_my_commented_post_app_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がコメントした投稿にコメントがあった際のアプリ通知', 'after' => 'feed_commented_on_my_post_email_flg'),
                    'feed_commented_on_my_commented_post_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がコメントした投稿にコメントがあった際のメール通知', 'after' => 'feed_commented_on_my_commented_post_app_flg'),
                    'circle_user_join_app_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が管理者の公開サークルに誰かが参加した際のアプリ通知', 'after' => 'feed_commented_on_my_commented_post_email_flg'),
                    'circle_user_join_email_flg'                    => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が管理者の公開サークルに誰かが参加した際のメール通知', 'after' => 'circle_user_join_app_flg'),
                    'circle_changed_privacy_setting_app_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のアプリ通知', 'after' => 'circle_user_join_email_flg'),
                    'circle_changed_privacy_setting_email_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のメール通知', 'after' => 'circle_changed_privacy_setting_app_flg'),
                    'circle_add_user_app_flg'                       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '誰かが自分をサークルに追加した際のアプリ通知', 'after' => 'circle_changed_privacy_setting_email_flg'),
                    'circle_add_user_email_flg'                     => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '誰かが自分をサークルに追加した際のメール通知', 'after' => 'circle_add_user_app_flg'),
                ),
            ),
            'drop_field'   => array(
                'notify_settings' => array('feed_app_flg', 'feed_email_flg', 'circle_app_flg', 'circle_email_flg'),
            ),
        ),
        'down' => array(
            'drop_field'   => array(
                'notify_settings' => array('feed_post_app_flg', 'feed_post_email_flg', 'feed_commented_on_my_post_app_flg', 'feed_commented_on_my_post_email_flg', 'feed_commented_on_my_commented_post_app_flg', 'feed_commented_on_my_commented_post_email_flg', 'circle_user_join_app_flg', 'circle_user_join_email_flg', 'circle_changed_privacy_setting_app_flg', 'circle_changed_privacy_setting_email_flg', 'circle_add_user_app_flg', 'circle_add_user_email_flg'),
            ),
            'create_field' => array(
                'notify_settings' => array(
                    'feed_app_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '投稿アプリ通知'),
                    'feed_email_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '投稿メール通知'),
                    'circle_app_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'サークル アプリ通知'),
                    'circle_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'サークル メール通知'),
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
