<?php
class ModifiedColumnComments0818 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'modified_column_comments_0818';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'message_files' => array(
					'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TopicID(belongsTo Topic Model)'),
					'message_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'MessageID(belongsTo Message Model)'),
					'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'AttachedFileID(belongsTo AttachedFile Model)'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
					'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'display order'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0','comment'=>''),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true,'comment'=>''),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true,'comment'=>''),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true,'comment'=>''),
				),
				'messages' => array(
					'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'topic ID(belongsTo Topic Model)'),
					'sender_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'UserID as Sender(belongsTo User Model)'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
					'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Body of message', 'charset' => 'utf8mb4'),
					'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'Message Type(1:Nomal,2:Add member,3:Remove member,4:Change topic name)'),
					'target_user_ids_if_member_changed' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'comma spalated list for target users(e.g. 1,2,3) if add or remove members.', 'charset' => 'utf8mb4'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0','comment'=>''),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true,'comment'=>''),
					'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary','comment'=>''),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true,'comment'=>''),
				),
				'topic_members' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TopicID(belongsTo Topic Model)'),
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'UserID as Topic Member(belongsTo User Model)'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
					'last_seen_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'message_id as last seen'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0','comment'=>''),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true,'comment'=>''),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true,'comment'=>''),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index','comment'=>''),
				),
				'topics' => array(
					'creator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'UserId as topic creator(belongsTo User Model)'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'TeamID(belongsTo Team Model)'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0','comment'=>''),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true,'comment'=>''),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true,'comment'=>''),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index','comment'=>''),
				),
			),
		),
        'down' => array(
            'alter_field' => array(
                'message_files' => array(
                    'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'トピックID(belongsToでTopicモデルに関連)'),
                    'message_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メッセージID(belongsToでMessageモデルに関連)'),
                    'attached_file_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ファイルID(belongsToでFileモデルに関連)'),
                    'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'index_num' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '表示順'),
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                ),
                'messages' => array(
                    'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'topic ID(belongsToでTopicモデルに関連)'),
                    'sender_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'メッセしたユーザID(belongsToでUserモデルに関連)'),
                    'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'メッセ本文', 'charset' => 'utf8mb4'),
                    'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 3, 'unsigned' => true, 'comment' => 'メッセタイプ(1:Nomal,2:メンバー追加,3:メンバー削除,4:トピック名変更)'),
                    'target_user_ids_if_member_changed' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '変更したユーザのuser_idをカンマ区切りで指定', 'charset' => 'utf8mb4'),
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                ),
                'topic_members' => array(
                    'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'トピック共有ユーザID'),
                    'topic_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'トピックID(belongsToでPostモデルに関連)'),
                    'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '共有ユーザID(belongsToでUserモデルに関連)'),
                    'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'last_seen_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '読んだ最後のmessage_id'),
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'トピックを削除した日付時刻'),
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'トピックを追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => 'トピックを更新した日付時刻'),
                ),
                'topics' => array(
                    'creator_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿作成ユーザID(belongsToでUserモデルに関連)'),
                    'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を削除した日付時刻'),
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '投稿を追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
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
