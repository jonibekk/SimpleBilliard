<?php
class AddRealUserNotificationSettingsToNotifySettings extends CakeMigration {

/**
 * Migration description
 * Why is file name `add_「real」_user_***`?
 * Because actually the value user configured on user notification settings page is not saved anywhere.
 * Refer https://jira.goalous.com/browse/GL-8281 to know detail
 * @var string
 */
	public $description = 'add_real_user_notification_settings_to_notify_settings';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'notify_settings' => array(
					'email_status' => array('type' => 'string', 'null' => false, 'default' => 'all', 'length' => 16, 'collate' => 'utf8_general_ci', 'comment' => 'Email notification setting', 'after' => 'user_id'),
					'mobile_status' => array('type' => 'string', 'null' => false, 'default' => 'all', 'length' => 16, 'collate' => 'utf8_general_ci', 'comment' => 'Mobile notification setting', 'after' => 'email_status'),
				),
			),
            'drop_field' => array(
                'notify_settings' => array('my_evaluator_evaluated_app_flg', 'my_evaluator_evaluated_email_flg', 'my_evaluator_evaluated_mobile_flg'),
            ),
        ),
		'down' => array(
			'drop_field' => array(
				'notify_settings' => array('email_status', 'mobile_status'),
			),
            'create_field' => array(
                'notify_settings' => array(
                    'my_evaluator_evaluated_app_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者が自分の評価をしたときのアプリ通知'),
                    'my_evaluator_evaluated_email_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者が自分の評価をしたときのメール通知'),
                    'my_evaluator_evaluated_mobile_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者が自分の評価をしたときのモバイル通知'),
                ),
            )
        ),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
        if($direction == 'up'){

        }
        return true;
	}


	// setting flg each type if status is primary,
    const PRIMARY_FLG_EACH_TYPE = [
        'feed_post'                             => 0,
        'feed_commented_on_my_post'             => 1,
        'feed_commented_on_my_commented_post'   => 1,
        'circle_user_join'                      => 0,
        'circle_changed_privacy_setting'        => 0,
        'circle_add_user'                       => 0,
        'my_goal_follow'                        => 0,
        'my_goal_collaborate'                   => 1,
        'my_goal_changed_by_leader'             => 1,
        'my_goal_target_for_evaluation'         => 1,
        'my_goal_as_leader_request_to_change'   => 1,
        'my_goal_not_target_for_evaluation'     => 1,
        'my_member_create_goal'                 => 1,
        'my_member_collaborate_goal'            => 0,
        'my_member_change_goal'                 => 1,
        'start_evaluation'                      => 1,
        'fleeze_evaluation'                     => 1,
        'start_can_oneself_evaluation'          => 0,
        'start_can_evaluate_as_evaluator'       => 1,
        'final_evaluation_is_done'              => 1,
        'feed_commented_on_my_action'           => 1,
        'feed_commented_on_my_commented_action' => 1,
        'feed_action'                           => 0,
        'user_joined_to_invited_team'           => 1,
        'feed_message'                          => 1,
        'setup_guide'                           => 0,
        'feed_mentioned_in'                     => 0
    ];

    /**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
        if($direction == 'up'){
            /** @var NotifySetting $NotifySetting */
            $NotifySetting = ClassRegistry::init('NotifySetting');

            // [Important]
            // Detect inconsistent data of notify setting is difficult and troublesome if search normally.
            // but it will be easier if you assume that there is inconsistent data from the beginning
            // At first, update both status to 'other'(this mean inconsistent data)
            // Then update status to 'all', 'primary', 'none', finally remaining 'other' status data are inconsistent.
            // all records - 'all', 'primary', 'none' sum records = inconsistent data ('other' status)
            $NotifySetting->updateAll(['email_status' => '"other"', 'mobile_status' => '"other"'], true);


            foreach (['email', 'mobile'] as $notifyTarget) {
                foreach (array_keys(NotifySetting::$TYPE_GROUP) as $typeGroup) {
                    $conditions = [];
                    $updateData = ["{$notifyTarget}_status" => '"'.$typeGroup.'"'];
                    foreach (self::PRIMARY_FLG_EACH_TYPE as $typeKey => $primaryFlg) {
                        $flg = 0;
                        if ($typeGroup == 'all'){
                            $flg = 1;
                        } else if ($typeGroup == 'primary') {
                            $flg = $primaryFlg;
                            // Deal as `feed_mentioned_in` is primary from now on
                            if ($typeKey === 'feed_mentioned_in') {
                                $updateData["{$typeKey}_{$notifyTarget}_flg"] = 1;
                            }
                        }

                        $conditions["{$typeKey}_{$notifyTarget}_flg"] = $flg;
                    }
                    // Update {email/mobile}_status by flg conditions
                    $NotifySetting->updateAll($updateData, $conditions);
                }
                // Finally update remaining 'other' status data
                // these are inconsistent data. so update both statuses and each flg like 'all' setting
                $updateReceiveAllNoti = array_merge(
                    ["{$notifyTarget}_status" => '"all"'],
                    array_fill_keys(array_keys($conditions), 1)
                );
                $NotifySetting->updateAll($updateReceiveAllNoti, ["{$notifyTarget}_status" => 'other']);
            }
        }
        return true;
	}
}
