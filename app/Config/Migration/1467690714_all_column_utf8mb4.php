<?php
class AllColumnUtf8mb4 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'all_column_utf8mb4';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'action_results' => array(
					'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '名前', 'charset' => 'utf8mb4'),
					'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アクションリザルト画像1', 'charset' => 'utf8mb4'),
					'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アクションリザルト画像2', 'charset' => 'utf8mb4'),
					'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アクションリザルト画像3', 'charset' => 'utf8mb4'),
					'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アクションリザルト画像4', 'charset' => 'utf8mb4'),
					'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アクションリザルト画像5', 'charset' => 'utf8mb4'),
					'note' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ノート', 'charset' => 'utf8mb4'),
				),
				'approval_histories' => array(
					'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント', 'charset' => 'utf8mb4'),
				),
				'attached_files' => array(
					'attached_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ファイル名', 'charset' => 'utf8mb4'),
					'file_ext' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ファイル拡張子', 'charset' => 'utf8mb4'),
				),
				'circles' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'サークル名', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サークルの説明', 'charset' => 'utf8mb4'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サークルロゴ画像', 'charset' => 'utf8mb4'),
				),
				'collaborators' => array(
					'role' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '役割', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '説明', 'charset' => 'utf8mb4'),
				),
				'comments' => array(
					'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント本文', 'charset' => 'utf8mb4'),
					'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント画像1', 'charset' => 'utf8mb4'),
					'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント画像2', 'charset' => 'utf8mb4'),
					'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント画像3', 'charset' => 'utf8mb4'),
					'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント画像4', 'charset' => 'utf8mb4'),
					'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント画像5', 'charset' => 'utf8mb4'),
					'site_info' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サイト情報', 'charset' => 'utf8mb4'),
					'site_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サイト画像', 'charset' => 'utf8mb4'),
				),
				'devices' => array(
					'device_token' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アプリインストール毎に発行される識別子', 'charset' => 'utf8mb4'),
				),
				'emails' => array(
					'email' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メアド', 'charset' => 'utf8mb4'),
					'email_token' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8mb4'),
				),
				'evaluate_scores' => array(
					'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '評価スコア名', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '評価スコアの説明', 'charset' => 'utf8mb4'),
				),
				'evaluations' => array(
					'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '評価コメント', 'charset' => 'utf8mb4'),
				),
				'goal_categories' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '名前', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '説明', 'charset' => 'utf8mb4'),
				),
				'goals' => array(
					'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '名前', 'charset' => 'utf8mb4'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ゴール画像', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '説明', 'charset' => 'utf8mb4'),
				),
				'group_visions' => array(
					'name' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'グループビジョン名', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'グループビジョンの説明', 'charset' => 'utf8mb4'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '画像', 'charset' => 'utf8mb4'),
				),
				'groups' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '部署名', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '部署の説明', 'charset' => 'utf8mb4'),
				),
				'invites' => array(
					'email' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メアド', 'charset' => 'utf8mb4'),
					'message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '招待メッセージ', 'charset' => 'utf8mb4'),
					'email_token' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8mb4'),
				),
				'job_categories' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '職種名', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '職種の説明', 'charset' => 'utf8mb4'),
				),
				'key_results' => array(
					'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '名前', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '説明', 'charset' => 'utf8mb4'),
				),
				'local_names' => array(
					'language' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 3, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8mb4'),
					'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '名', 'charset' => 'utf8mb4'),
					'last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '姓', 'charset' => 'utf8mb4'),
				),
				'member_types' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'タイプ名(正社員等', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'タイプの説明(正規雇用で企業に雇われた労働者等', 'charset' => 'utf8mb4'),
				),
				'messages' => array(
					'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'メッセージ本文', 'charset' => 'utf8mb4'),
				),
				'oauth_tokens' => array(
					'uid' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'プロバイダー固有ID', 'charset' => 'utf8mb4'),
					'token' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'トークン', 'charset' => 'utf8mb4'),
				),
				'post_shared_logs' => array(
					'shared_list' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '共有ログjson', 'charset' => 'utf8mb4'),
				),
				'posts' => array(
					'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿本文', 'charset' => 'utf8mb4'),
					'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿画像1', 'charset' => 'utf8mb4'),
					'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿画像2', 'charset' => 'utf8mb4'),
					'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿画像3', 'charset' => 'utf8mb4'),
					'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿画像4', 'charset' => 'utf8mb4'),
					'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '投稿画像5', 'charset' => 'utf8mb4'),
					'site_info' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サイト情報', 'charset' => 'utf8mb4'),
					'site_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'サイト画像', 'charset' => 'utf8mb4'),
				),
				'purposes' => array(
					'name' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '目的', 'charset' => 'utf8mb4'),
				),
				'recovery_codes' => array(
					'code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アプリ側で暗号化済のコード', 'charset' => 'utf8mb4'),
				),
				'send_mails' => array(
					'item' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アイテム(JSONエンコード)', 'charset' => 'utf8mb4'),
				),
				'subscribe_emails' => array(
					'email' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メアド', 'charset' => 'utf8mb4'),
				),
				'team_members' => array(
					'member_no' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'メンバーナンバー(組織内でメンバーを識別する為のナンバー。exp社員番号)', 'charset' => 'utf8mb4'),
					'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント', 'charset' => 'utf8mb4'),
				),
				'team_visions' => array(
					'name' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'チームビジョン名', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'チームビジョンの説明', 'charset' => 'utf8mb4'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '画像', 'charset' => 'utf8mb4'),
				),
				'teams' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'チーム名', 'charset' => 'utf8mb4'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'チームロゴ画像', 'charset' => 'utf8mb4'),
					'domain_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ドメイン名', 'charset' => 'utf8mb4'),
				),
				'threads' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'スレッド名', 'charset' => 'utf8mb4'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'スレッドの詳細', 'charset' => 'utf8mb4'),
				),
				'users' => array(
					'password' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'パスワード(暗号化)', 'charset' => 'utf8mb4'),
					'password_token' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'パスワードトークン(パスワード失念時の認証用)', 'charset' => 'utf8mb4'),
					'2fa_secret' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '２要素認証シークレットキー', 'charset' => 'utf8mb4'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'プロフィール画像', 'charset' => 'utf8mb4'),
					'language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8mb4'),
					'phone_no' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8mb4_general_ci', 'comment' => '電話番号', 'charset' => 'utf8mb4'),
					'hometown' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '出身地', 'charset' => 'utf8mb4'),
					'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'コメント', 'charset' => 'utf8mb4'),
					'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '英名', 'charset' => 'utf8mb4'),
					'last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => '英姓', 'charset' => 'utf8mb4'),
					'middle_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => '英ミドルネーム', 'charset' => 'utf8mb4'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'action_results' => array(
					'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
					'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像1', 'charset' => 'utf8'),
					'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像2', 'charset' => 'utf8'),
					'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像3', 'charset' => 'utf8'),
					'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像4', 'charset' => 'utf8'),
					'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アクションリザルト画像5', 'charset' => 'utf8'),
					'note' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ノート', 'charset' => 'utf8'),
				),
				'approval_histories' => array(
					'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント', 'charset' => 'utf8'),
				),
				'attached_files' => array(
					'attached_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル名', 'charset' => 'utf8'),
					'file_ext' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル拡張子', 'charset' => 'utf8'),
				),
				'circles' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'サークル名', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サークルの説明', 'charset' => 'utf8'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サークルロゴ画像', 'charset' => 'utf8'),
				),
				'collaborators' => array(
					'role' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '役割', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
				),
				'comments' => array(
					'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント本文', 'charset' => 'utf8'),
					'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像1', 'charset' => 'utf8'),
					'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像2', 'charset' => 'utf8'),
					'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像3', 'charset' => 'utf8'),
					'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像4', 'charset' => 'utf8'),
					'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント画像5', 'charset' => 'utf8'),
					'site_info' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サイト情報', 'charset' => 'utf8'),
					'site_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サイト画像', 'charset' => 'utf8'),
				),
				'devices' => array(
					'device_token' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アプリインストール毎に発行される識別子', 'charset' => 'utf8'),
				),
				'emails' => array(
					'email' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
					'email_token' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
				),
				'evaluate_scores' => array(
					'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価スコア名', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価スコアの説明', 'charset' => 'utf8'),
				),
				'evaluations' => array(
					'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '評価コメント', 'charset' => 'utf8'),
				),
				'goal_categories' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
				),
				'goals' => array(
					'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ゴール画像', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
				),
				'group_visions' => array(
					'name' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'グループビジョン名', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'グループビジョンの説明', 'charset' => 'utf8'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像', 'charset' => 'utf8'),
				),
				'groups' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '部署名', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '部署の説明', 'charset' => 'utf8'),
				),
				'invites' => array(
					'email' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
					'message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '招待メッセージ', 'charset' => 'utf8'),
					'email_token' => array('type' => 'string', 'null' => true, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアドトークン(メアド認証に必要なトークンを管理)', 'charset' => 'utf8'),
				),
				'job_categories' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '職種名', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '職種の説明', 'charset' => 'utf8'),
				),
				'key_results' => array(
					'name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名前', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '説明', 'charset' => 'utf8'),
				),
				'local_names' => array(
					'language' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 3, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8'),
					'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '名', 'charset' => 'utf8'),
					'last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '姓', 'charset' => 'utf8'),
				),
				'member_types' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'タイプ名(正社員等', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイプの説明(正規雇用で企業に雇われた労働者等', 'charset' => 'utf8'),
				),
				'messages' => array(
					'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メッセージ本文', 'charset' => 'utf8'),
				),
				'oauth_tokens' => array(
					'uid' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'プロバイダー固有ID', 'charset' => 'utf8'),
					'token' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'トークン', 'charset' => 'utf8'),
				),
				'post_shared_logs' => array(
					'shared_list' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '共有ログjson', 'charset' => 'utf8'),
				),
				'posts' => array(
					'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿本文', 'charset' => 'utf8'),
					'photo1_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像1', 'charset' => 'utf8'),
					'photo2_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像2', 'charset' => 'utf8'),
					'photo3_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像3', 'charset' => 'utf8'),
					'photo4_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像4', 'charset' => 'utf8'),
					'photo5_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '投稿画像5', 'charset' => 'utf8'),
					'site_info' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サイト情報', 'charset' => 'utf8'),
					'site_photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'サイト画像', 'charset' => 'utf8'),
				),
				'purposes' => array(
					'name' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '目的', 'charset' => 'utf8'),
				),
				'recovery_codes' => array(
					'code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アプリ側で暗号化済のコード', 'charset' => 'utf8'),
				),
				'send_mails' => array(
					'item' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'アイテム(JSONエンコード)', 'charset' => 'utf8'),
				),
				'subscribe_emails' => array(
					'email' => array('type' => 'string', 'null' => false, 'default' => null, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メアド', 'charset' => 'utf8'),
				),
				'team_members' => array(
					'member_no' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'メンバーナンバー(組織内でメンバーを識別する為のナンバー。exp社員番号)', 'charset' => 'utf8'),
					'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント', 'charset' => 'utf8'),
				),
				'team_visions' => array(
					'name' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'チームビジョン名', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'チームビジョンの説明', 'charset' => 'utf8'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '画像', 'charset' => 'utf8'),
				),
				'teams' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'チーム名', 'charset' => 'utf8'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'チームロゴ画像', 'charset' => 'utf8'),
					'domain_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'ドメイン名', 'charset' => 'utf8'),
				),
				'threads' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'スレッド名', 'charset' => 'utf8'),
					'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'スレッドの詳細', 'charset' => 'utf8'),
				),
				'users' => array(
					'password' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'パスワード(暗号化)', 'charset' => 'utf8'),
					'password_token' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'パスワードトークン(パスワード失念時の認証用)', 'charset' => 'utf8'),
					'2fa_secret' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '２要素認証シークレットキー', 'charset' => 'utf8'),
					'photo_file_name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'プロフィール画像', 'charset' => 'utf8'),
					'language' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8'),
					'phone_no' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '電話番号', 'charset' => 'utf8'),
					'hometown' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '出身地', 'charset' => 'utf8'),
					'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント', 'charset' => 'utf8'),
					'first_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '英名', 'charset' => 'utf8'),
					'last_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '英姓', 'charset' => 'utf8'),
					'middle_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '英ミドルネーム', 'charset' => 'utf8'),
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
