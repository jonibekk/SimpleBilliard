<?php
class ModifyCommentsForTopicRelatedTables0818 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'modify_comments_for_topic_related_tables_0818';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'message_files' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TopicID(belongsTo Topic Model)'),
					'message_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'MessageID(belongsTo Message Model)'),
					'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'AttachedFileID(belongsTo AttachedFile Model)'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
					'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'display order'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'topic_id' => array('column' => 'topic_id', 'unique' => 0),
						'message_id' => array('column' => 'message_id', 'unique' => 0),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'attached_file_id' => array('column' => 'attached_file_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'topic_members' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TopicID(belongsTo Topic Model)'),
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'UserID as Topic Member(belongsTo User Model)'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
					'last_seen_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'message_id as last seen'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'topic_id' => array('column' => 'topic_id', 'unique' => 0),
						'user_id' => array('column' => 'user_id', 'unique' => 0),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'modified' => array('column' => 'modified', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'topics' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'creator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'UserId as topic creator(belongsTo User Model)'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
					'title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 254, 'collate' => 'utf8mb4_general_ci', 'comment' => 'topic title', 'charset' => 'utf8mb4'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'user_id' => array('column' => 'creator_user_id', 'unique' => 0),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'modified' => array('column' => 'modified', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'messages' => array(
					'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'topic ID(belongsTo Topic Model)', 'after' => 'id'),
					'sender_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'UserID as Sender(belongsTo User Model)', 'after' => 'topic_id'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)', 'after' => 'sender_user_id'),
					'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'Message Type(1:Nomal,2:Add member,3:Remove member,4:Change topic name)', 'after' => 'body'),
					'target_user_ids_if_member_changed' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'comma spalated list for target users(e.g. 1,2,3) if add or remove members.', 'charset' => 'utf8mb4', 'after' => 'type'),
					'indexes' => array(
						'user_id' => array('column' => 'sender_user_id', 'unique' => 0),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
					),
				),
			),
			'drop_field' => array(
				'messages' => array('from_user_id', 'to_user_id', 'thread_id', 'indexes' => array('from_user_id', 'to_user_id', 'thread_id', 'PRIMARY')),
			),
			'alter_field' => array(
				'messages' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Body of message', 'charset' => 'utf8mb4'),
					'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary'),
				),
			),
			'drop_table' => array(
				'threads'
			),
		),
		'down' => array(
			'drop_table' => array(
				'message_files', 'topic_members', 'topics'
			),
			'drop_field' => array(
				'messages' => array('topic_id', 'sender_user_id', 'team_id', 'type', 'target_user_ids_if_member_changed', 'indexes' => array('user_id', 'team_id', 'PRIMARY')),
			),
			'create_field' => array(
				'messages' => array(
					'from_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)'),
					'to_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)'),
					'thread_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドID(belongsToでThreadモデルに関連)'),
					'indexes' => array(
						'from_user_id' => array('column' => 'from_user_id', 'unique' => 0),
						'to_user_id' => array('column' => 'to_user_id', 'unique' => 0),
						'thread_id' => array('column' => 'thread_id', 'unique' => 0),
						'PRIMARY' => array(),
					),
				),
			),
			'alter_field' => array(
				'messages' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'メッセージID'),
					'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'メッセージ本文', 'charset' => 'utf8mb4'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メッセージを追加した日付時刻'),
				),
			),
			'create_table' => array(
				'threads' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'スレッドID'),
					'from_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信元ユーザID(belongsToでUserモデルに関連)'),
					'to_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '送信先ユーザID(belongsToでUserモデルに関連)'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
					'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'スレッドタイプ(1:ゴール作成,2:Feedback)'),
					'status' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'スレッドステータス(1:Open,2:Close)'),
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'スレッド名', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'スレッドの詳細', 'charset' => 'utf8mb4'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'スレッドを削除した日付時刻'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドを追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'スレッドを更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'from_user_id' => array('column' => 'from_user_id', 'unique' => 0),
						'to_user_id' => array('column' => 'to_user_id', 'unique' => 0),
						'created' => array('column' => 'created', 'unique' => 0),
						'modified' => array('column' => 'modified', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
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
