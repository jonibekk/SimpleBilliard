<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 */

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array(
                           'engine' => 'Apc',
                       )
);

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

// CakePHPのオートローダーをいったん削除し、composerより先に評価されるように先頭に追加する
// https://github.com/composer/composer/commit/c80cb76b9b5082ecc3e5b53b1050f76bb27b127b を参照
spl_autoload_unregister(array('App', 'load'));
spl_autoload_register(array('App', 'load'), true, true);

Configure::write('Dispatcher.filters', array(
    'AssetDispatcher',
    'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
    'engine' => 'File',
    'types'  => array('notice', 'info', 'debug'),
    'file'   => 'debug',
));
CakeLog::config('error', array(
    'engine' => 'File',
    'types'  => array('warning', 'error', 'critical', 'alert', 'emergency'),
    'file'   => 'error',
));

Configure::write('Asset.timestamp', 'force');

App::build(array(
               'Vendor' => array(
                   ROOT . '/Vendor/',
               ),
               'Plugin' => array(
                   ROOT . '/Plugin/',
               ),
           )
);
CakePlugin::loadAll();

/**
 * Goalous独自定数
 */
switch (ENV_NAME) {
    case "www":
        define('SERVICE_NAME', "Goalous2.0");
        break;
    default:
        define('SERVICE_NAME', "[" . ENV_NAME . "]Goalous2.0");
        break;
}
//トークン期限
define('TOKEN_EXPIRE_SEC_REGISTER', 86400); //1day
define('TOKEN_EXPIRE_SEC_ADD_EMAIL', 86400); //1day
define('TOKEN_EXPIRE_SEC_INVITE', 1209600); //2weeks
//レイアウトファイル名
define('LAYOUT_ONE_COLUMN', "one_column");
define('LAYOUT_SETTING', "setting");
//セッションのモード
define('MODE_NEW_PROFILE', 1);
define('MODE_NEW_TEAM', 2);
//View側のモード
define('MODE_VIEW_TUTORIAL', 1);
//aws s3のurl
define('S3_BASE_URL', 'https://s3-ap-northeast-1.amazonaws.com');
define('S3_TRIM_PATH', '/upload/');
//リクエストされた時点のタイムスタンプ
define('REQUEST_TIMESTAMP', time());
//右カラム各要素の表示数
define('MY_GOALS_DISPLAY_NUMBER', 10);
define('MY_COLLABO_GOALS_DISPLAY_NUMBER', 10);
define('MY_FOLLOW_GOALS_DISPLAY_NUMBER', 20);
//通知リストボックス(ベルのアイコンのところ)内のアイテム数
define('NOTIFY_BELL_BOX_ITEMS_NUMBER', 5);
//通知リストページの1ブロックのアイテム数
define('NOTIFY_PAGE_ITEMS_NUMBER', 50);

//投稿フィードのアイテム数
define('POST_FEED_PAGE_ITEMS_NUMBER', 10);

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
