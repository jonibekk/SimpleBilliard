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

// Load common settings
require_once 'bootstrap_common.php';

/**
 * Configures default file logging options
 */
// changing path of error log, cause, doc root is changed every time of deployment by opsworks
$logPath = '/var/log/goalous/';
CakeLog::config('debug', [
    'engine' => LOG_ENGINE,
    'types'  => ['info', 'debug'],
    'file'   => 'debug',
    'path'   => $logPath
]);
CakeLog::config('error', [
    'engine' => LOG_ENGINE,
    'types'  => ['warning', 'error', 'critical', 'alert', 'notice'],
    'file'   => 'error',
    'path'   => $logPath
]);
CakeLog::config('emergency', [
    'engine' => LOG_ENGINE,
    'types'  => ['emergency'],
    'file'   => 'emergency',
    'path'   => $logPath
]);
