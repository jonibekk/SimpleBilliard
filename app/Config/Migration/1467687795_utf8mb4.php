<?php
class Utf8mb4 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'utf8mb4';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'access_users' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'action_result_files' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'action_results' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'approval_histories' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'attached_files' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'circle_insights' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'circle_members' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'circles' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'collaborators' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'comment_files' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'comment_likes' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'comment_mentions' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'comment_reads' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'comments' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'devices' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'emails' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'evaluate_scores' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'evaluate_terms' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'evaluation_settings' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'evaluations' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'evaluators' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'followers' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'given_badges' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'goal_categories' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'goals' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'group_insights' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'group_visions' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'groups' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'invites' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'job_categories' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'key_results' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'local_names' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'member_groups' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'member_types' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'messages' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'notify_settings' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'oauth_tokens' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'post_files' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'post_likes' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'post_mentions' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'post_reads' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'post_share_circles' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'post_share_users' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'post_shared_logs' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'posts' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'purposes' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'recovery_codes' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'send_mail_to_users' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'send_mails' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'subscribe_emails' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'team_insights' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'team_members' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'team_visions' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'teams' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'threads' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
				'users' => array(
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'access_users' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'action_result_files' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'action_results' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'approval_histories' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'attached_files' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'circle_insights' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'circle_members' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'circles' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'collaborators' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'comment_files' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'comment_likes' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'comment_mentions' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'comment_reads' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'comments' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'devices' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'emails' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'evaluate_scores' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'evaluate_terms' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'evaluation_settings' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'evaluations' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'evaluators' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'followers' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'given_badges' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'goal_categories' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'goals' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'group_insights' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'group_visions' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'groups' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'invites' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'job_categories' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'key_results' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'local_names' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'member_groups' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'member_types' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'messages' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'notify_settings' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'oauth_tokens' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'post_files' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'post_likes' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'post_mentions' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'post_reads' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'post_share_circles' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'post_share_users' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'post_shared_logs' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'posts' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'purposes' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'recovery_codes' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'send_mail_to_users' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'send_mails' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'subscribe_emails' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'team_insights' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'team_members' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'team_visions' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'teams' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'threads' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'users' => array(
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
