<?php

class AddFieldsToNotifyYSetting0510 extends CakeMigration
{

    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'add_fields_to_notify_y_setting_0510';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(
            'create_field' => array(
                'notify_settings' => array(
                    'my_goal_follow_app_flg'                          => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールがフォローされたときのアプリ通知', 'after' => 'circle_add_user_email_flg'),
                    'my_goal_follow_email_flg'                        => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールがフォローされたときのメール通知', 'after' => 'my_goal_follow_app_flg'),
                    'my_goal_collaborate_app_flg'                     => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールがコラボレートされたときのアプリ通知', 'after' => 'my_goal_follow_email_flg'),
                    'my_goal_collaborate_email_flg'                   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールがコラボレートされたときのメール通知', 'after' => 'my_goal_collaborate_app_flg'),
                    'my_goal_changed_by_leader_app_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールの内容がリーダーによって変更されたときのアプリ通知', 'after' => 'my_goal_collaborate_email_flg'),
                    'my_goal_changed_by_leader_email_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールの内容がリーダーによって変更されたときのメール通知', 'after' => 'my_goal_changed_by_leader_app_flg'),
                    'my_goal_target_for_evaluation_app_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールが評価対象となったときのアプリ通知', 'after' => 'my_goal_changed_by_leader_email_flg'),
                    'my_goal_target_for_evaluation_email_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールが評価対象となったときのメール通知', 'after' => 'my_goal_target_for_evaluation_app_flg'),
                    'my_goal_as_leader_request_to_change_app_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がリーダーのゴールが修正依頼を受けたときのアプリ通知', 'after' => 'my_goal_target_for_evaluation_email_flg'),
                    'my_goal_as_leader_request_to_change_email_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がリーダーのゴールが修正依頼を受けたときのメール通知', 'after' => 'my_goal_as_leader_request_to_change_app_flg'),
                    'my_goal_not_target_for_evaluation_app_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールが評価対象外となったときのアプリ通知', 'after' => 'my_goal_as_leader_request_to_change_email_flg'),
                    'my_goal_not_target_for_evaluation_email_flg'     => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分がオーナーのゴールが評価対象外となったときのメール通知', 'after' => 'my_goal_not_target_for_evaluation_app_flg'),
                    'my_member_create_goal_app_flg'                   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分(コーチとして)のメンバーがゴールを作成したときのアプリ通知', 'after' => 'my_goal_not_target_for_evaluation_email_flg'),
                    'my_member_create_goal_email_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分(コーチとして)のメンバーがゴールを作成したときのメール通知', 'after' => 'my_member_create_goal_app_flg'),
                    'my_member_collaborate_goal_app_flg'              => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分(コーチとして)のメンバーがゴールのコラボレーターとなったときのアプリ通知', 'after' => 'my_member_create_goal_email_flg'),
                    'my_member_collaborate_goal_email_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分(コーチとして)のメンバーがゴールのコラボレーターとなったときのメール通知', 'after' => 'my_member_collaborate_goal_app_flg'),
                    'my_member_change_goal_app_flg'                   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ゴールの修正依頼を受けた自分(コーチとして)のメンバーがゴール内容を修正したときのアプリ通知', 'after' => 'my_member_collaborate_goal_email_flg'),
                    'my_member_change_goal_email_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ゴールの修正依頼を受けた自分(コーチとして)のメンバーがゴール内容を修正したときのメール通知', 'after' => 'my_member_change_goal_app_flg'),
                    'start_evaluation_app_flg'                        => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するチームが評価開始となったときのアプリ通知', 'after' => 'my_member_change_goal_email_flg'),
                    'start_evaluation_email_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するチームが評価開始となったときのメール通知', 'after' => 'start_evaluation_app_flg'),
                    'fleeze_evaluation_app_flg'                       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するチームが評価凍結となったときのアプリ通知', 'after' => 'start_evaluation_email_flg'),
                    'fleeze_evaluation_email_flg'                     => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が所属するチームが評価凍結となったときのメール通知', 'after' => 'fleeze_evaluation_app_flg'),
                    'start_can_oneself_evaluation_app_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が自己評価できる状態になったときのアプリ通知', 'after' => 'fleeze_evaluation_email_flg'),
                    'start_can_oneself_evaluation_email_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が自己評価できる状態になったときのメール通知', 'after' => 'start_can_oneself_evaluation_app_flg'),
                    'start_can_evaluate_as_evaluator_app_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者としての自分が評価できる状態になったときのアプリ通知', 'after' => 'start_can_oneself_evaluation_email_flg'),
                    'start_can_evaluate_as_evaluator_email_flg'       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者としての自分が評価できる状態になったときのメール通知', 'after' => 'start_can_evaluate_as_evaluator_app_flg'),
                    'final_evaluation_is_done_app_flg'                => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の所属するチームの最終者が最終評価データをUploadしたときのアプリ通知', 'after' => 'start_can_evaluate_as_evaluator_email_flg'),
                    'final_evaluation_is_done_email_flg'              => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の所属するチームの最終者が最終評価データをUploadしたときのメール通知', 'after' => 'final_evaluation_is_done_app_flg'),
                    'feed_commented_on_my_action_app_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分のアクションに「コメント」されたときのアプリ通知', 'after' => 'final_evaluation_is_done_email_flg'),
                    'feed_commented_on_my_action_email_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分のアクションに「コメント」されたときのメール通知', 'after' => 'feed_commented_on_my_action_app_flg'),
                    'feed_commented_on_my_commented_action_app_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分のコメントしたアクションに「コメント」されたときのアプリ通知', 'after' => 'feed_commented_on_my_action_email_flg'),
                    'feed_commented_on_my_commented_action_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分のコメントしたアクションに「コメント」されたときのメール通知', 'after' => 'feed_commented_on_my_commented_action_app_flg'),
                    'feed_action_app_flg'                             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が閲覧可能なアクションがあったときのアプリ通知', 'after' => 'feed_commented_on_my_commented_action_email_flg'),
                    'feed_action_email_flg'                           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分が閲覧可能なアクションがあったときのメール通知', 'after' => 'feed_action_app_flg'),
                    'user_joined_to_invited_team_app_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の所属するチームへ招待したユーザーがチームに参加したときのアプリ通知', 'after' => 'feed_action_email_flg'),
                    'user_joined_to_invited_team_email_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自分の所属するチームへ招待したユーザーがチームに参加したときのメール通知', 'after' => 'user_joined_to_invited_team_app_flg'),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'notify_settings' => array('my_goal_follow_app_flg', 'my_goal_follow_email_flg', 'my_goal_collaborate_app_flg', 'my_goal_collaborate_email_flg', 'my_goal_changed_by_leader_app_flg', 'my_goal_changed_by_leader_email_flg', 'my_goal_target_for_evaluation_app_flg', 'my_goal_target_for_evaluation_email_flg', 'my_goal_as_leader_request_to_change_app_flg', 'my_goal_as_leader_request_to_change_email_flg', 'my_goal_not_target_for_evaluation_app_flg', 'my_goal_not_target_for_evaluation_email_flg', 'my_member_create_goal_app_flg', 'my_member_create_goal_email_flg', 'my_member_collaborate_goal_app_flg', 'my_member_collaborate_goal_email_flg', 'my_member_change_goal_app_flg', 'my_member_change_goal_email_flg', 'start_evaluation_app_flg', 'start_evaluation_email_flg', 'fleeze_evaluation_app_flg', 'fleeze_evaluation_email_flg', 'start_can_oneself_evaluation_app_flg', 'start_can_oneself_evaluation_email_flg', 'start_can_evaluate_as_evaluator_app_flg', 'start_can_evaluate_as_evaluator_email_flg', 'final_evaluation_is_done_app_flg', 'final_evaluation_is_done_email_flg', 'feed_commented_on_my_action_app_flg', 'feed_commented_on_my_action_email_flg', 'feed_commented_on_my_commented_action_app_flg', 'feed_commented_on_my_commented_action_email_flg', 'feed_action_app_flg', 'feed_action_email_flg', 'user_joined_to_invited_team_app_flg', 'user_joined_to_invited_team_email_flg'),
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
