<?php
/**
 * UserFixture
 *
 */
class UserFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => 'ユーザID', 'charset' => 'utf8'),
        'first_name'        => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '英名', 'charset' => 'utf8'),
        'last_name'         => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '英姓', 'charset' => 'utf8'),
        'middle_name'       => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '英ミドルネーム', 'charset' => 'utf8'),
        'local_first_name'  => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '母国の名', 'charset' => 'utf8'),
        'local_last_name'   => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '母国の姓', 'charset' => 'utf8'),
        'local_middle_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '母国のミドルネーム', 'charset' => 'utf8'),
        'gender_type'       => array('type' => 'integer', 'null' => true, 'default' => null, 'comment' => '性別(1:男,2:女)'),
        'birth_day'         => array('type' => 'date', 'null' => true, 'default' => null, 'comment' => '誕生日'),
        'hide_year_flg'     => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '誕生日の年を隠すフラグ'),
        'hometown'          => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '出身地', 'charset' => 'utf8'),
        'comment'           => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント', 'charset' => 'utf8'),
        'password'          => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'パスワード(暗号化)', 'charset' => 'utf8'),
        'password_token'    => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'パスワードトークン(パスワード失念時の認証用)', 'charset' => 'utf8'),
        'password_modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'パスワード最終更新日'),
        'no_pass_flg'       => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'パスワード未使用フラグ(ソーシャルログインのみ利用時)'),
        'profile_image_id'  => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'プロフィール画像ID(hasOneでImageモデルに関連)', 'charset' => 'utf8'),
        'primary_email_id'  => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'プライマリメールアドレスID(hasOneでEmailモデルに関連)', 'charset' => 'utf8'),
        'active_flg'        => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'アクティブフラグ(ユーザ認証済みの場合On)'),
        'last_login'        => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '最終ログイン日時'),
        'admin_flg'         => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '管理者フラグ(管理画面が開ける人)'),
        'default_team_id'   => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'デフォルトチーム(belongsToでTeamモデルに関連)', 'charset' => 'utf8'),
        'timezone'          => array('type' => 'float', 'null' => true, 'default' => null, 'comment' => 'タイムゾーン(UTCを起点とした時差)'),
        'auto_timezone_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自動タイムゾーンフラグ(Onの場合はOSからタイムゾーンを取得する)'),
        'language'          => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '言語(日本語ならjpn)', 'charset' => 'utf8'),
        'auto_language_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自動言語設定フラグ(Onの場合はブラウザから言語を取得する)'),
        'romanize_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'ローマ字表記フラグ(Onの場合は自分の名前がアプリ内で英語表記になる)'),
        'update_email_flg'  => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '更新情報メールフラグ(Onの場合はアプリから更新情報がメールで届く)'),
        'del_flg'           => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
        'deleted'           => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ユーザが退会した日付時刻'),
        'created'           => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ユーザーデータを登録した日付時刻'),
        'modified'          => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ユーザーデータを最後に更新した日付時刻'),
        'indexes'           => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters'   => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array(
        array(
            'id'                => '53746f19-f7ac-4db6-b715-0d9cac11b50b',
            'first_name'        => 'Lorem ipsum dolor sit amet',
            'last_name'         => 'Lorem ipsum dolor sit amet',
            'middle_name'       => 'Lorem ipsum dolor sit amet',
            'local_first_name'  => 'Lorem ipsum dolor sit amet',
            'local_last_name'   => 'Lorem ipsum dolor sit amet',
            'local_middle_name' => 'Lorem ipsum dolor sit amet',
            'gender_type'       => 1,
            'birth_day'         => '2014-05-15',
            'hide_year_flg'     => 1,
            'hometown'          => 'Lorem ipsum dolor sit amet',
            'comment'           => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'password'          => 'Lorem ipsum dolor sit amet',
            'password_token'    => 'Lorem ipsum dolor sit amet',
            'password_modified' => '2014-05-15 16:39:05',
            'no_pass_flg'       => 1,
            'profile_image_id'  => 'Lorem ipsum dolor sit amet',
            'primary_email_id'  => 'Lorem ipsum dolor sit amet',
            'active_flg'        => 1,
            'last_login'        => '2014-05-15 16:39:05',
            'admin_flg'         => 1,
            'default_team_id'   => 'Lorem ipsum dolor sit amet',
            'timezone'          => 1,
            'auto_timezone_flg' => 1,
            'language'          => 'Lorem ipsum dolor sit amet',
            'auto_language_flg' => 1,
            'romanize_flg'      => 1,
            'update_email_flg'  => 1,
            'del_flg'           => 1,
            'deleted'           => '2014-05-15 16:39:05',
            'created'           => '2014-05-15 16:39:05',
            'modified'          => '2014-05-15 16:39:05'
        ),
    );

}
