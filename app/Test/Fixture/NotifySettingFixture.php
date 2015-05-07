<?php

/**
 * NotifySettingFixture

 */
class NotifySettingFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                                            => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'user_id'                                       => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ユーザID(belongsToでUserモデルに関連)'),
        'feed_post_app_flg'                             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '閲覧可能な投稿があった際のアプリ通知'),
        'feed_post_email_flg'                           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '閲覧可能な投稿があった際のメール通知'),
        'feed_commented_on_my_post_app_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の投稿にコメントがあった際のアプリ通知'),
        'feed_commented_on_my_post_email_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の投稿にコメントがあった際のメール通知'),
        'feed_commented_on_my_commented_post_app_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がコメントした投稿にコメントがあった際のアプリ通知'),
        'feed_commented_on_my_commented_post_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がコメントした投稿にコメントがあった際のメール通知'),
        'circle_user_join_app_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が管理者の公開サークルに誰かが参加した際のアプリ通知'),
        'circle_user_join_email_flg'                    => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が管理者の公開サークルに誰かが参加した際のメール通知'),
        'circle_changed_privacy_setting_app_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のアプリ通知'),
        'circle_changed_privacy_setting_email_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のメール通知'),
        'circle_add_user_app_flg'                       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '誰かが自分をサークルに追加した際のアプリ通知'),
        'circle_add_user_email_flg'                     => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '誰かが自分をサークルに追加した際のメール通知'),
        'del_flg'                                       => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'                                       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'                                       => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '登録した日付時刻'),
        'modified'                                      => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
        'indexes'                                       => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'user_id' => array('column' => 'user_id', 'unique' => 0),
            'del_flg' => array('column' => 'del_flg', 'unique' => 0)
        ),
        'tableParameters'                               => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                                            => '',
            'user_id'                                       => '',
            'feed_post_app_flg'                             => 1,
            'feed_post_email_flg'                           => 1,
            'feed_commented_on_my_post_app_flg'             => 1,
            'feed_commented_on_my_post_email_flg'           => 1,
            'feed_commented_on_my_commented_post_app_flg'   => 1,
            'feed_commented_on_my_commented_post_email_flg' => 1,
            'circle_user_join_app_flg'                      => 1,
            'circle_user_join_email_flg'                    => 1,
            'circle_changed_privacy_setting_app_flg'        => 1,
            'circle_changed_privacy_setting_email_flg'      => 1,
            'circle_add_user_app_flg'                       => 1,
            'circle_add_user_email_flg'                     => 1,
            'del_flg'                                       => 1,
            'deleted'                                       => 1,
            'created'                                       => 1,
            'modified'                                      => 1
        ),
    );

}
