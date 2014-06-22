<?php
App::uses('AppController', 'Controller');

/**
 * Emails Controller
 *
 * @property Email              $Email
 * @property PaginatorComponent $Paginator
 * @property SessionComponent   $Session
 */
class EmailsController extends AppController
{

    /**
     * delete method
     *
     * @throws NotFoundException
     *
     * @param string $id
     *
     * @return void
     */
    public function delete($id = null)
    {
        $this->Email->id = $id;
        if (!$this->Email->exists()) {
            throw new NotFoundException(__('gl', "このメールアドレスは存在しません。"));
        }
        $this->request->allowMethod('post', 'delete');
        $this->Email->delete();
        $this->Pnotify->outSuccess(__d('gl', "メールアドレス変更をキャンセルしました。"));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }
}
