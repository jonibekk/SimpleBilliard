<?php
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 */
/**
 * Custom Inflector rules can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. Make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 */

/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter . By default CakePHP bundles two filters:
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 * Configure::write('Dispatcher.filters', array(
 *        'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *        'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 *        array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *        array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 * ));
 */
// composerのautoloadを読み込み
/** @noinspection PhpIncludeInspection */
require ROOT . '/Vendor/autoload.php';

// disable default locale setting for japanese file name problem
// 元々、日本語ファイルで問題あり。"あabcあ.png" が basename() で"abcあ.png"と扱われていた。 HTTP/2移行後から、日本語ファイル名が正しく扱われるようになった。<- 何が影響しているか判断できない。nginx? OS?
// 正しくないファイル名をhashしたものをs3に保存していたため、HTTP/2移行後から日本語ファイルの画像がリンク切れを起こすようになった。
// よって、元の(正しくない)設定に戻す。
setlocale(LC_ALL, "");

// CakePHPのオートローダーをいったん削除し、composerより先に評価されるように先頭に追加する
// https://github.com/composer/composer/commit/c80cb76b9b5082ecc3e5b53b1050f76bb27b127b を参照
spl_autoload_unregister(['App', 'load']);
spl_autoload_register(['App', 'load'], true, true);

Configure::write('Dispatcher.filters', [
    'AssetDispatcher',
    'CacheDispatcher'
]);

//slack setting
Configure::write('Slack', [
    'token'      => LOG_SLACK_TOKEN,
    'channel'    => LOG_SLACK_ERROR_CHANNEL,
    'username'   => 'cakephp',
    'icon_emoji' => ':cake:',
]);

Configure::write('Asset.timestamp', 'force');

App::build([
        'Vendor' => [
            ROOT . '/Vendor/',
        ],
        'Plugin' => [
            ROOT . '/Plugin/',
            // Batch Shells read as Plugins for classifying by namespaces
            ROOT . '/app/Console/Command/Batch/',
            ROOT . '/app/Console/Command/Development/',
            ROOT . '/app/Console/Command/DataMigration/',
        ],
    ]
);

//重複するコントローラを共存させる
if (isset($_SERVER['REQUEST_URI']) && preg_match('/^\/api\/v1/i', $_SERVER['REQUEST_URI'], $matches)) {
    App::build([
        'Controller' => [
            ROOT . DS . APP_DIR . DS . 'Controller' . DS . 'Api' . DS . 'V1' . DS,
            ROOT . DS . APP_DIR . DS . 'Controller' . DS,
        ],
    ]);
} else if (isset($_SERVER['REQUEST_URI']) && preg_match('/^\/api\//i', $_SERVER['REQUEST_URI'], $matches)) {
    App::build([
        'Controller' => [
            ROOT . DS . APP_DIR . DS . 'Controller' . DS . 'Api' . DS,
            ROOT . DS . APP_DIR . DS . 'Controller' . DS,
        ],
    ]);
}

// サービス層
App::build([
    'Service' => ['%s' . 'Service' . DS]
], App::REGISTER);

CakePlugin::loadAll();
//HtmlHelper UrlCache
CakePlugin::load('UrlCache');
Configure::write('UrlCache.active', true);

Configure::load("app.php");
Configure::load("country.php");

// CakePdf setting
CakePlugin::load('CakePdf', ['bootstrap' => true, 'routes' => true]);
Configure::write('CakePdf', array(
    'engine'      => 'CakePdf.WkHtmlToPdf',
    'binary'      => ROOT . '/etc/wkhtmltopdf.sh', // For ubuntu, wrapped by shell
    'options'     => array(
        'print-media-type' => false,
        'outline'          => true,
        'dpi'              => 96
    ),
    'margin'      => array(
        'bottom' => 5,
        'left'   => 5,
        'right'  => 5,
        'top'    => 5
    ),
    'orientation' => 'portrait',
    'encoding'    => 'UTF-8',
    'pageSize'    => 'A4',
));

// AdditionalExceptions
require APP . "Lib/Error/Exceptions.php";

/**
 * Goalous独自定数
 */
switch (ENV_NAME) {
    case "www":
        define('SERVICE_NAME', "Goalous");
        break;
    default:
        define('SERVICE_NAME', "[" . ENV_NAME . "]Goalous");
        break;
}
//トークン期限
define('TOKEN_EXPIRE_SEC_REGISTER', 86400); //1day
define('TOKEN_EXPIRE_SEC_ADD_EMAIL', 86400); //1day
define('TOKEN_EXPIRE_SEC_INVITE', 1209600); //2weeks
//レイアウトファイル名
define('LAYOUT_ONE_COLUMN', "one_column");
define('LAYOUT_TWO_COLUMN', "two_column");
define('LAYOUT_HOMEPAGE', "homepage");
define('LAYOUT_NO_HEADER', "no_header");
//セッションのモード
define('MODE_NEW_PROFILE', 1);
define('MODE_NEW_TEAM', 2);
//View側のモード
define('MODE_VIEW_TUTORIAL', 1);
//aws s3のurl
define('S3_BASE_URL', 'https://s3-ap-northeast-1.amazonaws.com');
define('S3_TRIM_PATH', '/upload/');
//office online viewerのbase url
define('OOV_BASE_URL', 'https://view.officeapps.live.com/op/view.aspx?src=');
//select2 query limit
define('SELECT2_QUERY_LIMIT', 200);
//リクエストされた時点のタイムスタンプ
define('REQUEST_TIMESTAMP', time());
// timestamp before 1hr
define('REQUEST_TIMESTAMP_ONE_HR_AGO', time() - 3600);
//右カラム各要素の表示数
define('MY_GOALS_DISPLAY_NUMBER', 5);
define('MY_COLLABO_GOALS_DISPLAY_NUMBER', 5);
define('MY_FOLLOW_GOALS_DISPLAY_NUMBER', 5);
define('MY_PREVIOUS_GOALS_DISPLAY_NUMBER', 5);
//通知リストボックス(ベルのアイコンのところ)内のアイテム数
define('NOTIFY_BELL_BOX_ITEMS_NUMBER', 7);
//通知リストページの1ブロックのアイテム数
define('NOTIFY_PAGE_ITEMS_NUMBER', 50);
//投稿フィードのアイテム数
define('POST_FEED_PAGE_ITEMS_NUMBER', 5);
//ゴール一覧のアイテム数
define('GOAL_INDEX_ITEMS_NUMBER', 20);
//マイページのアクション数
define('MY_GOAL_AREA_FIRST_VIEW_KR_COUNT', 2);
//マイページのアクション数
define('MY_PAGE_ACTION_NUMBER', 4);
//キューブ表示のアイテム数
define('MY_PAGE_CUBE_ACTION_IMG_NUMBER', 21);
//ゴールページのフォロワー数
define('GOAL_PAGE_FOLLOWER_NUMBER', 10);
//ゴールページのメンバー数
define('GOAL_PAGE_MEMBER_NUMBER', 10);
//ゴールページのキーリザルト数
define('GOAL_PAGE_KR_NUMBER', 10);
//ファイル一覧ページのアイテム数
define('FILE_LIST_PAGE_NUMBER', 20);
//キャッシュ用キーネーム
define('CACHE_KEY_TERM_CURRENT', 'current_term');
define('CACHE_KEY_TERM_NEXT', 'next_term');
define('CACHE_KEY_TERM_PREVIOUS', 'previous_term');
define('CACHE_KEY_CURRENT_TEAM', 'current_team');
define('CACHE_KEY_MEMBER_IS_ACTIVE', 'member_is_active');
define('CACHE_KEY_TEAM_LIST', 'team_list');
define('CACHE_KEY_TEAM_EVAL_SETTING', 'team_evaluation_setting');
define('CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE', 'channel_circles_not_hide');
define('CACHE_KEY_CHANNEL_CIRCLES_ALL', 'channel_circles_all');
define('CACHE_KEY_CHANNEL_FOLLOW_GOALS', 'channel_follow_goals');
define('CACHE_KEY_CHANNEL_COLLABO_GOALS', 'channel_collabo_goals');
define('CACHE_KEY_UNAPPROVED_COUNT', 'unapproved_count');
define('CACHE_KEY_EVALUABLE_COUNT', 'evaluable_count');
define('CACHE_KEY_ACTION_COUNT', 'action_count');
define('CACHE_KEY_MY_CIRCLE_LIST', 'my_circle_list');
define('CACHE_KEY_MY_MEMBER_STATUS', 'my_member_status');
define('CACHE_KEY_TEAM_ALL_CIRCLE', 'team_all_circle');
define('CACHE_KEY_TEAM_VISION', 'team_vision');
define('CACHE_KEY_GROUP_VISION', 'group_vision');
define('CACHE_KEY_MY_NOTIFY_SETTING', 'my_notify_setting');
define('CACHE_KEY_MY_PROFILE', 'my_profile');
define('CACHE_KEY_LABEL', 'label');
define('CACHE_KEY_EXPERIMENT', 'experiment');
define('CACHE_KEY_GOAL_KR_VALUES_DAILY_LOG', 'goal_kr_values_daily_log');
define('CACHE_KEY_USER_GOAL_KR_VALUES_DAILY_LOG', 'user_goal_kr_values_daily_log');
define('CACHE_KEY_KRS_IN_DASHBOARD', 'krs_in_dashboard');
define('CACHE_KEY_MY_KR_COUNT', 'my_kr_count');
define('CACHE_KEY_IS_STARTED_EVALUATION', 'is_started_evaluation');
define('CACHE_KEY_MY_ACTIONABLE_GOALS', 'my_goals_for_top_action');
define('CACHE_KEY_BEFORE_CHANGE_TIMEZONE', 'before_change_timezone');
define('CACHE_KEY_TEAM_CREDIT_CARD_EXPIRE_DATE', 'team_credit_card_expire_date');

//Referer value name of URL(for Google analytics)
define('REFERER_STATUS_DEFAULT', '?st=def');
define('REFERER_STATUS_LOGIN', '?st=in');
define('REFERER_STATUS_SIGNUP_WITH_INVITING', '?st=new&invite=yes');
define('REFERER_STATUS_SIGNUP_WITH_NOT_INVITING', '?st=new&invite=no');
define('REFERER_STATUS_INVITED_USER_EXIST', '?st=inv_ex');
define('REFERER_STATUS_INVITED_USER_EXIST_BY_EMAIL', '?st=inv_ex&from=email');
define('REFERER_STATUS_INVITED_USER_EXIST_BY_CSV', '?st=inv_ex&from=csv');
define('REFERER_STATUS_INVITED_USER_NOT_EXIST_BY_EMAIL', '?st=inv_no&from=email');
define('REFERER_STATUS_INVITED_USER_NOT_EXIST_BY_CSV', '?st=inv_no&from=csv');

define('SETUP_GUIDE_EXIPIRE_SEC_BY_REDIS', 1209600); //2 weeks

// For Demo Site
define('IS_DEMO', ENV_NAME === 'demo'); // How many hours the demo is reset
define('DEMO_RESET_HOURS', 24); // How many hours the demo is reset
define('DEMO_RESET_TIME', 3); // Every Day 3AM

App::uses('GoalousLog', 'Log');
App::uses('CustomLogger', 'Utility');
App::uses('ConfigKeyResult', 'Config');

/**
 * 共通関数
 */

/**
 * 改行を除去
 *
 * @param $str
 *
 * @return string
 */
function del_line_break($str)
{
    return str_replace(array("\r\n", "\n", "\r"), '', $str);
}

/**
 * @param $val
 *
 * @return $val or null
 */
function viaIsSet(&$val)
{
    return isset($val) ? $val : null;
}

/**
 * @param int $val
 *
 * @return bool
 */
function isOver2($val)
{
    if ($val >= 2) {
        return true;
    }
    return false;
}

function copyKeyName(array $from, array $to)
{
    if (count($from) !== count($to)) {
        return false;
    }
    return array_combine(array_keys($from), $to);
}
