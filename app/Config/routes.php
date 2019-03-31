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

if (isset($_SERVER['REQUEST_URI']) && preg_match('/^\/api\/v1/i', $_SERVER['REQUEST_URI'], $matches)) {
    /**
     * @deprecated This /api/v1/* is old API routing, do not touch about routing.
     */
    $apiVersions = 'v1';
    /**
     * REST
     * actionなし
     */
    Router::connect('/api/:apiVersion/:controller',
        ['action' => 'list', 'prefix' => 'get', '[method]' => 'GET'],
        ['apiVersion' => $apiVersions, 'id' => '[0-9]+', 'pass' => ['id']]
    );
    Router::connect('/api/:apiVersion/:controller/:id',
        ['action' => 'detail', 'prefix' => 'get', '[method]' => 'GET'],
        ['apiVersion' => $apiVersions, 'id' => '[0-9]+', 'pass' => ['id']]
    );
    Router::connect('/api/:apiVersion/:controller',
        ['action' => 'post', '[method]' => 'POST'],
        ['apiVersion' => $apiVersions]
    );
    Router::connect('/api/:apiVersion/:controller/:id',
        ['action' => 'put', '[method]' => 'PUT'],
        ['apiVersion' => $apiVersions, 'id' => '[0-9]+', 'pass' => ['id']]
    );
    Router::connect('/api/:apiVersion/:controller/:id',
        ['action' => 'delete', '[method]' => 'DELETE'],
        ['apiVersion' => $apiVersions, 'id' => '[0-9]+', 'pass' => ['id']]
    );

    /**
     * REST
     * actionあり
     */
    Router::connect('/api/:apiVersion/:controller/:action',
        ['prefix' => 'get', '[method]' => 'GET'],
        ['apiVersion' => $apiVersions]
    );
    Router::connect('/api/:apiVersion/:controller/:id/:action',
        ['prefix' => 'get', '[method]' => 'GET'],
        ['apiVersion' => $apiVersions, 'id' => '[0-9]+', 'pass' => ['id']]
    );
    Router::connect('/api/:apiVersion/:controller/:action',
        ['prefix' => 'post', '[method]' => 'POST'],
        ['apiVersion' => $apiVersions]
    );
    Router::connect('/api/:apiVersion/:controller/:id/:action',
        ['prefix' => 'post', '[method]' => 'POST'],
        ['apiVersion' => $apiVersions, 'id' => '[0-9]+', 'pass' => ['id']]
    );
    Router::connect('/api/:apiVersion/:controller/:id/:action',
        ['prefix' => 'put', '[method]' => 'PUT'],
        ['apiVersion' => $apiVersions, 'id' => '[0-9]+', 'pass' => ['id']]
    );
    Router::connect('/api/:apiVersion/:controller/:id/:action',
        ['prefix' => 'delete', '[method]' => 'DELETE'],
        ['apiVersion' => $apiVersions, 'id' => '[0-9]+', 'pass' => ['id']]
    );
} elseif (isset($_SERVER['REQUEST_URI']) && preg_match('/^\/api\//i', $_SERVER['REQUEST_URI'], $matches)) {
    /**
     * Special cases
     */
    Router::connect('/api/notifications/:action',
        ['controller' => 'notifications', 'prefix' => 'put', '[method]' => 'PUT']);
    Router::connect('/api/me',
        ['controller' => 'me', 'action' => 'detail', 'prefix' => 'get', '[method]' => 'GET']);
    Router::connect('/api/me/switch_team',
        ['controller' => 'me', 'action' => 'switch_team', 'prefix' => 'put', '[method]' => 'PUT']);

    /**
     * REST Endpoints with version in header 'X-API-Version'
     * No action
     */
    Router::connect('/api/:controller',
        ['action' => 'list', 'prefix' => 'get', '[method]' => 'GET']);
    Router::connect('/api/:controller/:id',
        ['action' => 'detail', 'prefix' => 'get', '[method]' => 'GET'],
        ['id' => '[0-9]+', 'pass' => ['id']]);
    Router::connect('/api/:controller',
        ['action' => 'post', '[method]' => 'POST']);
    Router::connect('/api/:controller/:id',
        ['action' => 'put', '[method]' => 'PUT'],
        ['id' => '[0-9]+', 'pass' => ['id']]);
    Router::connect('/api/:controller/:id',
        ['action' => 'delete', '[method]' => 'DELETE'],
        ['id' => '[0-9]+', 'pass' => ['id']]);
    /**
     * REST Endpoints with version in header 'X-API-Version'
     * With action
     */
    Router::connect('/api/:controller/:action',
        ['prefix' => 'get', '[method]' => 'GET']);
    Router::connect('/api/:controller/:id/:action',
        ['prefix' => 'get', '[method]' => 'GET'],
        ['id' => '[0-9]+', 'pass' => ['id']]);
    Router::connect('/api/:controller/:action',
        ['prefix' => 'post', '[method]' => 'POST']);
    Router::connect('/api/:controller/:id/:action',
        ['prefix' => 'post', '[method]' => 'POST'],
        ['id' => '[0-9]+', 'pass' => ['id']]);
    Router::connect('/api/:controller/:id/:action',
        ['prefix' => 'put', '[method]' => 'PUT'],
        ['id' => '[0-9]+', 'pass' => ['id']]);
    Router::connect('/api/:controller/:id/:action',
        ['prefix' => 'delete', '[method]' => 'DELETE'],
        ['id' => '[0-9]+', 'pass' => ['id']]);

}

/**
 * エイリアス
 */
Router::connect('/', ['controller' => 'pages', 'action' => 'home']);
Router::connect('/notify_id::notify_id/*', ['controller' => 'pages', 'action' => 'home']);
Router::connect('/common_form/:common_form_type/*', ['controller' => 'pages', 'action' => 'home']);
Router::connect('/team_id::team_id/*', ['controller' => 'pages', 'action' => 'home']);
Router::connect('/from::from/*', ['controller' => 'pages', 'action' => 'home']);
Router::connect('/circle_feed/:circle_id/*', ['controller' => 'posts', 'action' => 'feed',]);
Router::connect('/post_permanent/:post_id/*', ['controller' => 'posts', 'action' => 'feed',]);
Router::connect('/ajax_post_permanent/:post_id/*', ['controller' => 'posts', 'action' => 'ajax_get_feed',]);
Router::connect('/ajax_circle_feed/:circle_id/*', ['controller' => 'posts', 'action' => 'ajax_circle_feed',]);
Router::connect('/goals/:id/edit',
    ['controller' => 'goals', 'action' => 'edit', '[method]' => 'GET'],
    ['id' => '[0-9]+', 'pass' => ['id']]
);
Router::connect('/circles/:id/edit',
    ['controller' => 'circles', 'action' => 'edit', '[method]' => 'GET'],
    ['id' => '[0-9]+', 'pass' => ['id']]
);
Router::connect('/topics/:topic_id/detail', ['controller' => 'topics', 'action' => 'detail']);
Router::connect('/topics/:topic_id/add_members', ['controller' => 'topics', 'action' => 'add_members']);
Router::connect('/topics/:topic_id/search_messages', ['controller' => 'topics', 'action' => 'search_messages']);

Router::connect('/evaluator_settings/:user_id/detail', ['controller' => 'evaluator_settings', 'action' => 'detail']);

/**
 * Redirect
 */
// TODO: Should delete after implemented app force update
Router::redirect('/after_click:SubHeaderMenuGoal', ['controller' => 'goals', 'action' => 'kr_progress']);

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
Router::connect('/:lang/', ['controller' => 'pages', 'action' => 'home'], ['lang' => 'ja|en']);
Router::connect('/:pagename', ['controller' => 'pages', 'action' => 'lp'],
    ['pagename' => 'features|pricing|terms|campaign_terms|privacy_policy|law|contact_thanks', 'pass' => ['pagename']]);
Router::connect('/:lang/:pagename', ['controller' => 'pages', 'action' => 'lp'],
    [
        'pagename' => 'features|pricing|terms|campaign_terms|privacy_policy|law|contact_thanks',
        'lang'     => 'ja|en',
        'pass'     => ['pagename']
    ]);
Router::connect('/:pagename', ['controller' => 'pages', 'action' => 'app_version_unsupported'],
    ['pagename' => 'app_force_update|app_force_install', 'pass' => ['pagename']]);

// Allow '.pdf' extension
Router::parseExtensions('pdf');

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
