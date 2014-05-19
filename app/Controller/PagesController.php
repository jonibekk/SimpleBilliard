<?php
/**
 * Static content controller.
 * This file will render views from views/pages/
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link          http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */

/** @noinspection PhpInconsistentReturnPointsInspection */
class PagesController extends AppController
{

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array();

    /**
     * Displays a view
     *
     * @throws NotFoundException
     * @throws Exception
     * @throws MissingViewException
     * @internal param \What $mixed page to display
     * @return $this->redirect('/') or void
     */
    public function display()
    {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect('/');
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        //title_for_layoutはAppControllerで設定
        $this->set(compact('page', 'subpage'));

        //ログインしている場合とそうでない場合の切り分け
        if ($this->Auth->user()) {
            if ($path[0] == 'home') {
                //homeの場合
                if ($this->Session->read('completed_today_alist')) {
                    //全てのリストが完了している場合はモーダル表示
                    $this->set('completed_today_alist', true);
                    $this->Session->delete('completed_today_alist');
                }

                $this->render('logged_in_home');
            }
            else {
                $this->render(implode('/', $path));
            }
        }
        else {
            //ログインしていない場合のヘッダー
            //$this -> layout = 'not_logged_in';
            $this->layout = 'homepage';
            //現在の登録ユーザ数
            //$user_count = $this->User->getAllUsersCount();
            $user_count = 0;
            $this->set(compact('user_count'));
            if ($path[0] == 'logged_in_home') {
                $this->render('home');
            }
            else {
                $this->render(implode('/', $path));
            }
        }
        return $this->render(implode('/', $path));
    }

    public function beforeFilter()
    {
        parent::beforeFilter();
    }
}
