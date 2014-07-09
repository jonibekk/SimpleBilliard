<?php
App::uses('AppController', 'Controller');

/**
 * Posts Controller
 *
 * @property Post $Post
 */
class PostsController extends AppController
{
    /**
     * add method
     *
     * @throws RuntimeException
     * @return void
     */
    public function add()
    {
        if ($this->request->is('post')) {
            if ($this->Post->add($this->request->data)) {
                $this->Pnotify->outSuccess(__d('gl', "投稿しました。"));
            }
            else {
                $this->Pnotify->outError(__d('gl', "投稿に失敗しました。"));
            }
            $this->redirect($this->referer());
        }
        else {
            throw new RuntimeException(__d('exception', "不正な画面遷移です。"));
        }
    }
}
