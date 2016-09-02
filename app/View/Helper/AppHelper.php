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
            case 'teams':
                if($action_name === 'invite') $need_display_footer = false;
                break;
            case 'posts':
                if($action_name === 'message') $need_display_footer = false;
                break;
            case 'users':
                if($action_name === 'register_with_invite') $need_display_footer = false;
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

    /**
     * View開始のHTMLコメント
     *
     * @return string
     */
    function viewStartComment()
    {
        return $this->viewComment();
    }

    /**
     * View終了のHTMLコメント
     *
     * @return string
     */
    function viewEndComment()
    {
        return $this->viewComment(true);
    }

    /**
     * ViewのHTMLコメント出力共通処理
     *
     * @return string
     */
    private function viewComment($viewEnd = false)
    {
        if (!in_array(ENV_NAME, ["dev", "local"])) {
             return "";
        }
        $debugBackTrace = debug_backtrace();
        $viewPath = $debugBackTrace[1]["file"];
        $status = $viewEnd ? "END" : "START";
        return "<!-- {$status} {$viewPath} -->";
    }
}
