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
Router::connect('/', ['controller' => 'pages', 'action' => 'display', 'home']);
Router::connect('/circle_feed/:circle_id/*', ['controller' => 'pages', 'action' => 'display', 'home']);
/**
 * トップページの言語切り換えの為のルーティング設定。
 * PagesControllerにのみ影響する。
 */
Router::connect('/:lang/', ['controller' => 'pages', 'action' => 'display', 'home'], ['lang' => 'ja|en']);
Router::connect('/:pagename', ['controller' => 'pages', 'action' => 'display'],
                ['pagename' => 'features|pp|tos', 'pass' => ['pagename']]);
Router::connect('/:lang/:pagename', ['controller' => 'pages', 'action' => 'display'],
                ['pagename' => 'features|pp|tos', 'lang' => 'ja|en', 'pass' => ['pagename']]);
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
