<?php

class ChangeNotifySettings0807 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'change_notify_settings_0807';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'alter_field'  => array(
                'actions'         => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                ),
                'notify_settings' => array(
                    'feed_post_email_flg'                             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '閲覧可能な投稿があった際のメール通知'),
                    'feed_commented_on_my_post_email_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分の投稿にコメントがあった際のメール通知'),
                    'feed_commented_on_my_commented_post_email_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がコメントした投稿にコメントがあった際のメール通知'),
                    'circle_user_join_email_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が管理者の公開サークルに誰かが参加した際のメール通知'),
                    'circle_changed_privacy_setting_email_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のメール通知'),
                    'circle_add_user_email_flg'                       => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '誰かが自分をサークルに追加した際のメール通知'),
                    'my_goal_follow_email_flg'                        => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がオーナーのゴールがフォローされたときのメール通知'),
                    'my_goal_collaborate_email_flg'                   => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がオーナーのゴールがコラボレートされたときのメール通知'),
                    'my_goal_changed_by_leader_email_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がオーナーのゴールの内容がリーダーによって変更されたときのメール通知'),
                    'my_goal_target_for_evaluation_email_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がオーナーのゴールが評価対象となったときのメール通知'),
                    'my_goal_as_leader_request_to_change_email_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がリーダーのゴールが修正依頼を受けたときのメール通知'),
                    'my_goal_not_target_for_evaluation_email_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分がオーナーのゴールが評価対象外となったときのメール通知'),
                    'my_member_create_goal_email_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分(コーチとして)のメンバーがゴールを作成したときのメール通知'),
                    'my_member_collaborate_goal_email_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分(コーチとして)のメンバーがゴールのコラボレーターとなったときのメール通知'),
                    'my_member_change_goal_email_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'ゴールの修正依頼を受けた自分(コーチとして)のメンバーがゴール内容を修正したときのメール通知'),
                    'start_evaluation_email_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が所属するチームが評価開始となったときのメール通知'),
                    'fleeze_evaluation_email_flg'                     => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が所属するチームが評価凍結となったときのメール通知'),
                    'start_can_oneself_evaluation_email_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が自己評価できる状態になったときのメール通知'),
                    'start_can_evaluate_as_evaluator_email_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価者としての自分が評価できる状態になったときのメール通知'),
                    'final_evaluation_is_done_email_flg'              => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分の所属するチームの最終者が最終評価データをUploadしたときのメール通知'),
                    'feed_commented_on_my_action_email_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分のアクションに「コメント」されたときのメール通知'),
                    'feed_commented_on_my_commented_action_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分のコメントしたアクションに「コメント」されたときのメール通知'),
                    'feed_action_email_flg'                           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が閲覧可能なアクションがあったときのメール通知'),
                    'user_joined_to_invited_team_email_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分の所属するチームへ招待したユーザーがチームに参加したときのメール通知'),
                ),
            ),
            'create_field' => array(
                'notify_settings' => array(
                    'feed_message_app_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が閲覧可能なメッセージがあったときのアプリ通知', 'after' => 'user_joined_to_invited_team_email_flg'),
                    'feed_message_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '自分が閲覧可能なメッセージがあったときのメール通知', 'after' => 'feed_message_app_flg'),
                ),
            ),
        ),
        'down' => array(
            'alter_field' => array(
                'actions'         => array(
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                ),
                'notify_settings' => array(
                    'feed_post_email_flg'                             => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'feed_commented_on_my_post_email_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'feed_commented_on_my_commented_post_email_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'circle_user_join_email_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'circle_changed_privacy_setting_email_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'circle_add_user_email_flg'                       => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'my_goal_follow_email_flg'                        => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'my_goal_collaborate_email_flg'                   => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'my_goal_changed_by_leader_email_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'my_goal_target_for_evaluation_email_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'my_goal_as_leader_request_to_change_email_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'my_goal_not_target_for_evaluation_email_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'my_member_create_goal_email_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'my_member_collaborate_goal_email_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'my_member_change_goal_email_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'start_evaluation_email_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'fleeze_evaluation_email_flg'                     => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'start_can_oneself_evaluation_email_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'start_can_evaluate_as_evaluator_email_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'final_evaluation_is_done_email_flg'              => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'feed_commented_on_my_action_email_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'feed_commented_on_my_commented_action_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'feed_action_email_flg'                           => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                    'user_joined_to_invited_team_email_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0'),
                ),
            ),
            'drop_field'  => array(
                'notify_settings' => array('feed_message_app_flg', 'feed_message_email_flg'),
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
