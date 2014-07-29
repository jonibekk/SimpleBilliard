<?php
App::uses('AppController', 'Controller');

/**
 * Circles Controller
 *
 * @property Circle $Circle
 */
class CirclesController extends AppController
{
    /**
     * add method
     *
     * @return void
     */
    public function add()
    {
        $this->request->allowMethod('post');
        $this->Circle->create();
        if ($this->Circle->add($this->request->data)) {
            $this->Pnotify->outSuccess(__d('gl', "サークルを作成しました。"));
        }
        else {
            $this->Pnotify->outError(__d('gl', "サークルの作成に失敗しました。"));
        }
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

}
