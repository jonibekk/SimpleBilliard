<?php
class EditBasicTables extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'comment_reads' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'コメント読んだID', 'charset' => 'utf8'),
					'comment_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コメントID(belongsToでcommentモデルに関連)', 'charset' => 'utf8'),
					'user_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
					'team_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => 'コメントを削除した日付時刻'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => 'コメントを追加した日付時刻'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => 'コメントを更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'groups' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '部署ID', 'charset' => 'utf8'),
					'team_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
					'parent_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '上位部署ID(belongsToで同モデルに関連)', 'charset' => 'utf8'),
					'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '部署名', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '部署の説明', 'charset' => 'utf8'),
					'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '部署を削除した日付時刻'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '部署を追加した日付時刻'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '部署を更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'post_reads' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿読んだID', 'charset' => 'utf8'),
					'post_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
					'user_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
					'team_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '投稿を削除した日付時刻'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '投稿を追加した日付時刻'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '投稿を更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'comments' => array(
					'comment_read_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'comment' => 'コメント読んだ数(comment_readsテーブルにレコードが追加されたらカウントアップされる)', 'after' => 'comment_like_count'),
				),
				'posts' => array(
					'post_read_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'comment' => '読んだ数', 'after' => 'post_like_count'),
				),
				'team_members' => array(
					'group_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '部署ID(belongsToでgroupモデルに関連)', 'charset' => 'utf8', 'after' => 'coach_user_id'),
				),
				'teams' => array(
					'start_term_month' => array('type' => 'integer', 'null' => false, 'default' => '4', 'comment' => '期間の開始月(入力可能な値は1〜12)', 'after' => 'domain_name'),
					'border_months' => array('type' => 'integer', 'null' => false, 'default' => '6', 'comment' => '期間の月数(４半期なら3,半年なら6, 0を認めない)', 'after' => 'start_term_month'),
				),
				'users' => array(
					'password_modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => 'パスワード最終更新日', 'after' => 'password_token'),
				),
			),
			'drop_field' => array(
				'comments' => array('comment_reading_count',),
				'posts' => array('post_reading_count',),
				'team_members' => array('division_id',),
			),
			'alter_field' => array(
				'invites' => array(
					'to_user_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '招待先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
				),
			),
			'drop_table' => array(
				'comment_readings', 'divisions', 'post_readings'
			),
		),
		'down' => array(
			'drop_table' => array(
				'comment_reads', 'groups', 'post_reads'
			),
			'drop_field' => array(
				'comments' => array('comment_read_count',),
				'posts' => array('post_read_count',),
				'team_members' => array('group_id',),
				'teams' => array('start_term_month', 'border_months',),
				'users' => array('password_modified',),
			),
			'create_field' => array(
				'comments' => array(
					'comment_reading_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'comment' => 'コメント読んだ数(comment_readingsテーブルにレコードが追加されたらカウントアップされる)'),
				),
				'posts' => array(
					'post_reading_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'comment' => '読んだ数'),
				),
				'team_members' => array(
					'division_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '部署ID(belongsToでDivisionモデルに関連)', 'charset' => 'utf8'),
				),
			),
			'alter_field' => array(
				'invites' => array(
					'to_user_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '招待先ユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
				),
			),
			'create_table' => array(
				'comment_readings' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'コメント読んだID', 'charset' => 'utf8'),
					'comment_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'コメントID(belongsToでcommentモデルに関連)', 'charset' => 'utf8'),
					'user_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
					'team_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => 'コメントを削除した日付時刻'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => 'コメントを追加した日付時刻'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => 'コメントを更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'divisions' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '部署ID', 'charset' => 'utf8'),
					'team_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
					'parent_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '上位部署ID(belongsToで同モデルに関連)', 'charset' => 'utf8'),
					'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '部署名', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '部署の説明', 'charset' => 'utf8'),
					'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'アクティブフラグ(Offの場合は選択が不可能。古いものを無効にする場合に使用)'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '部署を削除した日付時刻'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '部署を追加した日付時刻'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '部署を更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'post_readings' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '投稿読んだID', 'charset' => 'utf8'),
					'post_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '投稿ID(belongsToでPostモデルに関連)', 'charset' => 'utf8'),
					'user_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => '読んだしたユーザID(belongsToでUserモデルに関連)', 'charset' => 'utf8'),
					'team_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'チームID(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '投稿を削除した日付時刻'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '投稿を追加した日付時刻'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '投稿を更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
		return true;
	}
}
