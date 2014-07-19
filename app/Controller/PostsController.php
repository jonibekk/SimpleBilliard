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
        $this->_ajaxPreProcess();

        if (!$page_num) {
            $page_num = 1;
        }

        $posts = $this->Post->get($page_num);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/posts');
        $html = $response->__toString();
        $result = array(
            'html' => $html
        );
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_comment($post_id)
    {
        $this->_ajaxPreProcess();

        $comments = $this->Post->Comment->getPostsComment($post_id, 3);
        $this->set(compact('comments'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/ajax_comments');
        $html = $response->__toString();
        $result = array(
            'html' => $html
        );
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_post_like($post_id)
    {
        $this->_ajaxPreProcess();
        $res = $this->Post->PostLike->changeLike($post_id);
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_comment_like($comment_id)
    {
        $this->_ajaxPreProcess();
        $res = $this->Post->Comment->CommentLike->changeLike($comment_id);
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_get_post_liked_users($post_id)
    {
        $this->_ajaxPreProcess();
        $liked_users = $this->Post->PostLike->getLikedUsers($post_id);
        $this->set(compact('liked_users'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/modal_post_liked_users');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_comment_liked_users($comment_id)
    {
        $this->_ajaxPreProcess();
        $liked_users = $this->Post->Comment->CommentLike->getLikedUsers($comment_id);
        $this->set(compact('liked_users'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/modal_comment_liked_users');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function _ajaxPreProcess()
    {
        if (!$this->request->is('ajax')) {
            throw new RuntimeException(__d('exception', '不正なアクセスです。'));
        }
        Configure::write('debug', 0);
        $this->layout = 'ajax';
        $this->viewPath = 'Elements';
    }

    public function _ajaxGetResponse($result)
    {
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