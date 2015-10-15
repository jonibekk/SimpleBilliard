<?php

class AddMobileNotifySettings1014 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_mobile_notify_settings_1014';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'notify_settings' => array(
                    'feed_post_mobile_flg'                             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '閲覧可能な投稿があった際のモバイル通知', 'after' => 'feed_post_email_flg'),
                    'feed_commented_on_my_post_mobile_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分の投稿にコメントがあった際のモバイル通知', 'after' => 'feed_commented_on_my_post_email_flg'),
                    'feed_commented_on_my_commented_post_mobile_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がコメントした投稿にコメントがあった際のモバイル通知', 'after' => 'feed_commented_on_my_commented_post_email_flg'),
                    'circle_user_join_mobile_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が管理者の公開サークルに誰かが参加した際のモバイル通知', 'after' => 'circle_user_join_email_flg'),
                    'circle_changed_privacy_setting_mobile_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のモバイル通知', 'after' => 'circle_changed_privacy_setting_email_flg'),
                    'circle_add_user_mobile_flg'                       => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '誰かが自分をサークルに追加した際のモバイル通知', 'after' => 'circle_add_user_email_flg'),
                    'my_goal_follow_mobile_flg'                        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がオーナーのゴールがフォローされたときのモバイル通知', 'after' => 'my_goal_follow_email_flg'),
                    'my_goal_collaborate_mobile_flg'                   => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がオーナーのゴールがコラボレートされたときのモバイル通知', 'after' => 'my_goal_collaborate_email_flg'),
                    'my_goal_changed_by_leader_mobile_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がオーナーのゴールの内容がリーダーによって変更されたときのモバイル通知', 'after' => 'my_goal_changed_by_leader_email_flg'),
                    'my_goal_target_for_evaluation_mobile_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がオーナーのゴールが評価対象となったときのモバイル通知', 'after' => 'my_goal_target_for_evaluation_email_flg'),
                    'my_goal_as_leader_request_to_change_mobile_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がリーダーのゴールが修正依頼を受けたときのモバイル通知', 'after' => 'my_goal_as_leader_request_to_change_email_flg'),
                    'my_goal_not_target_for_evaluation_mobile_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がオーナーのゴールが評価対象外となったときのモバイル通知', 'after' => 'my_goal_not_target_for_evaluation_email_flg'),
                    'my_member_create_goal_mobile_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分(コーチとして)のメンバーがゴールを作成したときのモバイル通知', 'after' => 'my_member_create_goal_email_flg'),
                    'my_member_collaborate_goal_mobile_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分(コーチとして)のメンバーがゴールのコラボレーターとなったときのモバイル通知', 'after' => 'my_member_collaborate_goal_email_flg'),
                    'my_member_change_goal_mobile_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'ゴールの修正依頼を受けた自分(コーチとして)のメンバーがゴール内容を修正したときのモバイル通知', 'after' => 'my_member_change_goal_email_flg'),
                    'start_evaluation_mobile_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が所属するチームが評価開始となったときのモバイル通知', 'after' => 'start_evaluation_email_flg'),
                    'fleeze_evaluation_mobile_flg'                     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が所属するチームが評価凍結となったときのモバイル通知', 'after' => 'fleeze_evaluation_email_flg'),
                    'start_can_oneself_evaluation_mobile_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が自己評価できる状態になったときのモバイル通知', 'after' => 'start_can_oneself_evaluation_email_flg'),
                    'start_can_evaluate_as_evaluator_mobile_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価者としての自分が評価できる状態になったときのモバイル通知', 'after' => 'start_can_evaluate_as_evaluator_email_flg'),
                    'final_evaluation_is_done_mobile_flg'              => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分の所属するチームの最終者が最終評価データをUploadしたときのモバイル通知', 'after' => 'final_evaluation_is_done_email_flg'),
                    'feed_commented_on_my_action_mobile_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分のアクションに「コメント」されたときのモバイル通知', 'after' => 'feed_commented_on_my_action_email_flg'),
                    'feed_commented_on_my_commented_action_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分のコメントしたアクションに「コメント」されたときのモバイル通知', 'after' => 'feed_commented_on_my_commented_action_email_flg'),
                    'feed_action_mobile_flg'                           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が閲覧可能なアクションがあったときのモバイル通知', 'after' => 'feed_action_email_flg'),
                    'user_joined_to_invited_team_mobile_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分の所属するチームへ招待したユーザーがチームに参加したときのモバイル通知', 'after' => 'user_joined_to_invited_team_email_flg'),
                    'feed_message_mobile_flg'                          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が閲覧可能なメッセージがあったときのモバイル通知', 'after' => 'feed_message_email_flg'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'notify_settings' => array('feed_post_mobile_flg', 'feed_commented_on_my_post_mobile_flg', 'feed_commented_on_my_commented_post_mobile_flg', 'circle_user_join_mobile_flg', 'circle_changed_privacy_setting_mobile_flg', 'circle_add_user_mobile_flg', 'my_goal_follow_mobile_flg', 'my_goal_collaborate_mobile_flg', 'my_goal_changed_by_leader_mobile_flg', 'my_goal_target_for_evaluation_mobile_flg', 'my_goal_as_leader_request_to_change_mobile_flg', 'my_goal_not_target_for_evaluation_mobile_flg', 'my_member_create_goal_mobile_flg', 'my_member_collaborate_goal_mobile_flg', 'my_member_change_goal_mobile_flg', 'start_evaluation_mobile_flg', 'fleeze_evaluation_mobile_flg', 'start_can_oneself_evaluation_mobile_flg', 'start_can_evaluate_as_evaluator_mobile_flg', 'final_evaluation_is_done_mobile_flg', 'feed_commented_on_my_action_mobile_flg', 'feed_commented_on_my_commented_action_mobile_flg', 'feed_action_mobile_flg', 'user_joined_to_invited_team_mobile_flg', 'feed_message_mobile_flg'),
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
