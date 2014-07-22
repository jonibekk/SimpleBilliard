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

    public function ajax_get_feed($page_num = null)
    {
        if (!$this->request->is('ajax')) {
            throw new RuntimeException(__d('exception', '不正なアクセスです。'));
        }
        //パラメータ初期値
        $feed_type_id = null;
        $created = null;
        if (!$page_num) {
            $page_num = 1;
        }
        Configure::write('debug', 0);
        $this->layout = 'ajax';
        $this->viewPath = 'Elements';

        $posts = $this->Post->get($page_num);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/posts');
        $html = $response->__toString();
        $result = array(
            'html' => $html
        );
        //レスポンスをjsonで生成
        $this->response->type('json');
        $this->response->body(json_encode($result));
        return $this->response;
    }

    public function ajax_get_comment($post_id)
    {
        if (!$this->request->is('ajax')) {
            throw new RuntimeException(__d('exception', '不正なアクセスです。'));
        }
        //パラメータ初期値
        $feed_type_id = null;
        $created = null;
        Configure::write('debug', 0);
        $this->layout = 'ajax';
        $this->viewPath = 'Elements';

        $comments = $this->Post->Comment->getPostsComment($post_id, 3);
        $this->set(compact('comments'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/ajax_comments');
        $html = $response->__toString();
        $result = array(
            'html' => $html
        );
        //レスポンスをjsonで生成
        $this->response->type('json');
        $this->response->body(json_encode($result));
        return $this->response;
    }

    public function comment_add()
    {
        if ($this->request->is('post')) {
            if ($this->Post->Comment->add($this->request->data)) {
                $this->Pnotify->outSuccess(__d('gl', "コメントしました。"));
            }
            else {
                $this->Pnotify->outError(__d('gl', "コメントに失敗しました。"));
            }
            $this->redirect($this->referer());
        }
        else {
            throw new RuntimeException(__d('exception', "不正な画面遷移です。"));
        }
    }
}