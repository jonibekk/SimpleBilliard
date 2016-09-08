<?php
/**
 * Routes configuration
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 */

/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
if (env('HTTP_X_FORWARDED_PROTO') == 'https') {
    Router::fullbaseUrl('https://' . env('HTTP_HOST'));
}

/**
 * Api
 * # versionを追加する場合
 * 1. app/Controller/Api以下にバージョン番号のディレクトリを作成し、コントローラを配置
 * 2. 以下2つのRouterのapiVersionに新しいバージョン番号を追加
 * # REST以外のもの
 * アクションメソッドとして適宜追加していく。(Routingの設定を増やさない)
 * ただし、Methodを限定する事。
 */
$apiVersions = 'v1|v2|';
/**
 * REST
 */
Router::connect('/api/:apiVersion/:controller',
    [
        ['action' => 'index', 'method' => 'GET', 'id' => false],
        ['action' => 'view', 'method' => 'GET', 'id' => true],
        ['action' => 'add', 'method' => 'POST', 'id' => false],
        ['action' => 'edit', 'method' => 'PUT', 'id' => true],
        ['action' => 'delete', 'method' => 'DELETE', 'id' => true],
        ['action' => 'update', 'method' => 'POST', 'id' => true]
    ],
    [
        'apiVersion' => $apiVersions
    ]
);
/**
 * With Id
 * idがURIに含まれる事をaction内で確認
 */
Router::connect('/api/:apiVersion/:controller/:id/:action',
    [],
    [
        'apiVersion' => $apiVersions,
        'id'         => '[0-9]+',
    ]
);
/**
 * Without Id
 */
Router::connect('/api/:apiVersion/:controller/:action', [], ['apiVersion' => $apiVersions]);

/**
 * エイリアス
 */
Router::connect('/', ['controller' => 'pages', 'action' => 'display', 'home']);
Router::connect('/notify_id::notify_id/*', ['controller' => 'pages', 'action' => 'display', 'home']);
Router::connect('/after_click::after_click/*', ['controller' => 'pages', 'action' => 'display', 'home']);
Router::connect('/common_form/:common_form_type/*', ['controller' => 'pages', 'action' => 'display', 'home']);
Router::connect('/team_id::team_id/*', ['controller' => 'pages', 'action' => 'display', 'home']);
Router::connect('/from::from/*', ['controller' => 'pages', 'action' => 'display', 'home']);
Router::connect('/circle_feed/:circle_id/*', ['controller' => 'posts', 'action' => 'feed',]);
Router::connect('/post_permanent/:post_id/*', ['controller' => 'posts', 'action' => 'feed',]);
Router::connect('/ajax_post_permanent/:post_id/*', ['controller' => 'posts', 'action' => 'ajax_get_feed',]);
Router::connect('/ajax_circle_feed/:circle_id/*', ['controller' => 'posts', 'action' => 'ajax_circle_feed',]);

/**
 * コンタクト系の一部のactionは独自の処理が必要な為、actionメソッドをPagesControllerに配置している
 * 言語指定あり
 */
Router::connect('/:action/*', ['controller' => 'pages'],
    ['action' => 'contact|contact_confirm|contact_send']);
Router::connect('/:lang/:action', ['controller' => 'pages'],
    ['action' => 'contact|contact_confirm|contact_send', 'lang' => 'ja|en']);

/**
 * トップページの言語切り換えの為のルーティング設定。
 * 言語指定あり
 * PagesControllerにのみ影響する。
 */
Router::connect('/:lang/', ['controller' => 'pages', 'action' => 'display', 'home'], ['lang' => 'ja|en']);
Router::connect('/:pagename', ['controller' => 'pages', 'action' => 'display'],
    ['pagename' => 'features|pricing|terms|privacy_policy|law|contact_thanks', 'pass' => ['pagename']]);
Router::connect('/:lang/:pagename', ['controller' => 'pages', 'action' => 'display'],
    [
        'pagename' => 'features|pricing|terms|privacy_policy|law|contact_thanks',
        'lang'     => 'ja|en',
        'pass'     => ['pagename']
    ]);

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
/** @noinspection PhpIncludeInspection */
require CAKE . 'Config' . DS . 'routes.php';
