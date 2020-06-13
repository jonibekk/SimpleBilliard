<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * NotifySettingFixture
 */
class NotifySettingFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                                               => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ),
        'user_id'                                          => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index',
            'comment'  => 'ユーザID(belongsToでUserモデルに関連)'
        ),
        'email_status'    => array(
            'type'    => 'string',
            'length'  => 16,
            'null'    => false,
            'default' => 'all',
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'email sttus',
            'charset' => 'utf8mb4'
        ),
        'mobile_status'    => array(
            'type'    => 'string',
            'length'  => 16,
            'null'    => false,
            'default' => 'all',
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'mobile sttus',
            'charset' => 'utf8mb4'
        ),
        'feed_post_app_flg'                                => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '閲覧可能な投稿があった際のアプリ通知'
        ),
        'feed_post_email_flg'                              => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '閲覧可能な投稿があった際のメール通知'
        ),
        'feed_post_mobile_flg'                             => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '閲覧可能な投稿があった際のモバイル通知'
        ),
        'feed_commented_on_my_post_app_flg'                => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分の投稿にコメントがあった際のアプリ通知'
        ),
        'feed_commented_on_my_post_email_flg'              => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分の投稿にコメントがあった際のメール通知'
        ),
        'feed_commented_on_my_post_mobile_flg'             => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分の投稿にコメントがあった際のモバイル通知'
        ),
        'feed_commented_on_my_commented_post_app_flg'      => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がコメントした投稿にコメントがあった際のアプリ通知'
        ),
        'feed_commented_on_my_commented_post_email_flg'    => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がコメントした投稿にコメントがあった際のメール通知'
        ),
        'feed_commented_on_my_commented_post_mobile_flg'   => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がコメントした投稿にコメントがあった際のモバイル通知'
        ),
        'circle_user_join_app_flg'                         => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が管理者の公開サークルに誰かが参加した際のアプリ通知'
        ),
        'circle_user_join_email_flg'                       => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が管理者の公開サークルに誰かが参加した際のメール通知'
        ),
        'circle_user_join_mobile_flg'                      => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が管理者の公開サークルに誰かが参加した際のモバイル通知'
        ),
        'circle_changed_privacy_setting_app_flg'           => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のアプリ通知'
        ),
        'circle_changed_privacy_setting_email_flg'         => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のメール通知'
        ),
        'circle_changed_privacy_setting_mobile_flg'        => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が所属するサークルのプライバシー設定が変更になった際のモバイル通知'
        ),
        'circle_add_user_app_flg'                          => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '誰かが自分をサークルに追加した際のアプリ通知'
        ),
        'circle_add_user_email_flg'                        => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '誰かが自分をサークルに追加した際のメール通知'
        ),
        'circle_add_user_mobile_flg'                       => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '誰かが自分をサークルに追加した際のモバイル通知'
        ),
        'my_goal_follow_app_flg'                           => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールがフォローされたときのアプリ通知'
        ),
        'my_goal_follow_email_flg'                         => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールがフォローされたときのメール通知'
        ),
        'my_goal_follow_mobile_flg'                        => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールがフォローされたときのモバイル通知'
        ),
        'my_goal_collaborate_app_flg'                      => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールがコラボレートされたときのアプリ通知'
        ),
        'my_goal_collaborate_email_flg'                    => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールがコラボレートされたときのメール通知'
        ),
        'my_goal_collaborate_mobile_flg'                   => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールがコラボレートされたときのモバイル通知'
        ),
        'my_goal_changed_by_leader_app_flg'                => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールの内容がリーダーによって変更されたときのアプリ通知'
        ),
        'my_goal_changed_by_leader_email_flg'              => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールの内容がリーダーによって変更されたときのメール通知'
        ),
        'my_goal_changed_by_leader_mobile_flg'             => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールの内容がリーダーによって変更されたときのモバイル通知'
        ),
        'my_goal_target_for_evaluation_app_flg'            => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールが評価対象となったときのアプリ通知'
        ),
        'my_goal_target_for_evaluation_email_flg'          => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールが評価対象となったときのメール通知'
        ),
        'my_goal_target_for_evaluation_mobile_flg'         => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールが評価対象となったときのモバイル通知'
        ),
        'my_goal_as_leader_request_to_change_app_flg'      => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がリーダーのゴールが修正依頼を受けたときのアプリ通知'
        ),
        'my_goal_as_leader_request_to_change_email_flg'    => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がリーダーのゴールが修正依頼を受けたときのメール通知'
        ),
        'my_goal_as_leader_request_to_change_mobile_flg'   => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がリーダーのゴールが修正依頼を受けたときのモバイル通知'
        ),
        'my_goal_not_target_for_evaluation_app_flg'        => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールが評価対象外となったときのアプリ通知'
        ),
        'my_goal_not_target_for_evaluation_email_flg'      => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールが評価対象外となったときのメール通知'
        ),
        'my_goal_not_target_for_evaluation_mobile_flg'     => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分がオーナーのゴールが評価対象外となったときのモバイル通知'
        ),
        'my_member_create_goal_app_flg'                    => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分(コーチとして)のメンバーがゴールを作成したときのアプリ通知'
        ),
        'my_member_create_goal_email_flg'                  => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分(コーチとして)のメンバーがゴールを作成したときのメール通知'
        ),
        'my_member_create_goal_mobile_flg'                 => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分(コーチとして)のメンバーがゴールを作成したときのモバイル通知'
        ),
        'my_member_collaborate_goal_app_flg'               => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分(コーチとして)のメンバーがゴールのコラボレーターとなったときのアプリ通知'
        ),
        'my_member_collaborate_goal_email_flg'             => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分(コーチとして)のメンバーがゴールのコラボレーターとなったときのメール通知'
        ),
        'my_member_collaborate_goal_mobile_flg'            => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分(コーチとして)のメンバーがゴールのコラボレーターとなったときのモバイル通知'
        ),
        'my_member_change_goal_app_flg'                    => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => 'ゴールの修正依頼を受けた自分(コーチとして)のメンバーがゴール内容を修正したときのアプリ通知'
        ),
        'my_member_change_goal_email_flg'                  => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => 'ゴールの修正依頼を受けた自分(コーチとして)のメンバーがゴール内容を修正したときのメール通知'
        ),
        'my_member_change_goal_mobile_flg'                 => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => 'ゴールの修正依頼を受けた自分(コーチとして)のメンバーがゴール内容を修正したときのモバイル通知'
        ),
        'start_evaluation_app_flg'                         => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が所属するチームが評価開始となったときのアプリ通知'
        ),
        'start_evaluation_email_flg'                       => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が所属するチームが評価開始となったときのメール通知'
        ),
        'start_evaluation_mobile_flg'                      => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が所属するチームが評価開始となったときのモバイル通知'
        ),
        'fleeze_evaluation_app_flg'                        => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が所属するチームが評価凍結となったときのアプリ通知'
        ),
        'fleeze_evaluation_email_flg'                      => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が所属するチームが評価凍結となったときのメール通知'
        ),
        'fleeze_evaluation_mobile_flg'                     => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が所属するチームが評価凍結となったときのモバイル通知'
        ),
        'start_can_oneself_evaluation_app_flg'             => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が自己評価できる状態になったときのアプリ通知'
        ),
        'start_can_oneself_evaluation_email_flg'           => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が自己評価できる状態になったときのメール通知'
        ),
        'start_can_oneself_evaluation_mobile_flg'          => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が自己評価できる状態になったときのモバイル通知'
        ),
        'start_can_evaluate_as_evaluator_app_flg'          => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '評価者としての自分が評価できる状態になったときのアプリ通知'
        ),
        'start_can_evaluate_as_evaluator_email_flg'        => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '評価者としての自分が評価できる状態になったときのメール通知'
        ),
        'start_can_evaluate_as_evaluator_mobile_flg'       => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '評価者としての自分が評価できる状態になったときのモバイル通知'
        ),
        'my_evaluator_evaluated_app_flg'                   => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '評価者が自分の評価をしたときのアプリ通知'
        ),
        'my_evaluator_evaluated_email_flg'                 => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '評価者が自分の評価をしたときのメール通知'
        ),
        'my_evaluator_evaluated_mobile_flg'                => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '評価者が自分の評価をしたときのモバイル通知'
        ),
        'final_evaluation_is_done_app_flg'                 => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分の所属するチームの最終者が最終評価データをUploadしたときのアプリ通知'
        ),
        'final_evaluation_is_done_email_flg'               => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分の所属するチームの最終者が最終評価データをUploadしたときのメール通知'
        ),
        'final_evaluation_is_done_mobile_flg'              => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分の所属するチームの最終者が最終評価データをUploadしたときのモバイル通知'
        ),
        'feed_commented_on_my_action_app_flg'              => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分のアクションに「コメント」されたときのアプリ通知'
        ),
        'feed_commented_on_my_action_email_flg'            => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分のアクションに「コメント」されたときのメール通知'
        ),
        'feed_commented_on_my_action_mobile_flg'           => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分のアクションに「コメント」されたときのモバイル通知'
        ),
        'feed_commented_on_my_commented_action_app_flg'    => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分のコメントしたアクションに「コメント」されたときのアプリ通知'
        ),
        'feed_commented_on_my_commented_action_email_flg'  => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分のコメントしたアクションに「コメント」されたときのメール通知'
        ),
        'feed_commented_on_my_commented_action_mobile_flg' => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分のコメントしたアクションに「コメント」されたときのモバイル通知'
        ),
        'feed_action_app_flg'                              => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が閲覧可能なアクションがあったときのアプリ通知'
        ),
        'feed_action_email_flg'                            => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が閲覧可能なアクションがあったときのメール通知'
        ),
        'feed_action_mobile_flg'                           => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が閲覧可能なアクションがあったときのモバイル通知'
        ),
        'user_joined_to_invited_team_app_flg'              => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分の所属するチームへ招待したユーザーがチームに参加したときのアプリ通知'
        ),
        'user_joined_to_invited_team_email_flg'            => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分の所属するチームへ招待したユーザーがチームに参加したときのメール通知'
        ),
        'user_joined_to_invited_team_mobile_flg'           => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分の所属するチームへ招待したユーザーがチームに参加したときのモバイル通知'
        ),
        'feed_message_app_flg'                             => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が閲覧可能なメッセージがあったときのアプリ通知'
        ),
        'feed_message_email_flg'                           => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が閲覧可能なメッセージがあったときのメール通知'
        ),
        'feed_message_mobile_flg'                          => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '1',
            'comment' => '自分が閲覧可能なメッセージがあったときのモバイル通知'
        ),
        'del_flg'                                          => array(
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0',
            'comment' => '削除フラグ'
        ),
        'deleted'                                          => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '削除した日付時刻'
        ),
        'created'                                          => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '登録した日付時刻'
        ),
        'modified'                                         => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '更新した日付時刻'
        ),
        'indexes'                                          => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'user_id' => array('column' => 'user_id', 'unique' => 0)
        ),
        'tableParameters'                                  => array(
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'engine'  => 'InnoDB'
        )
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                                            => '1',
            'user_id'                                       => '',
            'email_status'                                  => 'all',
            'mobile_status'                                 => 'all',
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
