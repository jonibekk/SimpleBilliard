<?php /**
 * Application level View Helper
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('Helper', 'View');
App::uses('UrlCacheAppHelper', 'UrlCache.View/Helper');

/**
 * Application helper
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends UrlCacheAppHelper
{
    function needDisplayFooter()
    {
        $controller_name = viaIsSet($this->request->params['controller']);
        $action_name = viaIsSet($this->request->params['action']);
        $need_display_footer = true;

        switch ($controller_name) {
            case 'posts':
                if($action_name === 'message') $need_display_footer = false;
                break;
            case 'setup':
                $need_display_footer = false;
                break;
            case 'signup':
                $need_display_footer = false;
                break;
        }

        return $need_display_footer;
    }
}
