<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
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

        try {
            $this->render(implode('/', $path));
        } catch (MissingViewException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
        return $this->render(implode('/', $path));
    }
}
