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
        $this->request->allowMethod('post');
        if (isset($this->request->data['Post']['body']) && !empty($this->request->data['Post']['body'])) {
            $ogp = $this->Ogp->getOgpByUrlInText($this->request->data['Post']['body']);
            if (isset($ogp['title']) && isset($ogp['description'])) {
                $this->request->data['Post']['site_info'] = json_encode($ogp);
            }
        }
        if ($this->Post->add($this->request->data)) {
            $this->Pnotify->outSuccess(__d('gl', "投稿しました。"));
        }
        else {
            if (!empty($this->Post->validationErrors)) {
                $error_msg = array_shift($this->Post->validationErrors);
                $this->Pnotify->outError($error_msg[0], ['title' => __d('gl', "投稿に失敗しました。")]);
            }
            else {
                $this->Pnotify->outError(__d('gl', "投稿に失敗しました。"));
            }
        }
        $this->redirect($this->referer());
    }

    /**
     * post_delete method
     *
     * @throws NotFoundException
     *
     * @param string $id
     *
     * @return void
     */
    public function post_delete($id)
    {
        $this->Post->id = $id;
        if (!$this->Post->exists()) {
            throw new NotFoundException(__('gl', "この投稿は存在しません。"));
        }
        if (!$this->Post->isOwner($this->Auth->user('id')) && !$this->User->TeamMember->myStatusWithTeam['TeamMember']['admin_flg']) {
            throw new NotFoundException(__('gl', "この投稿はあなたのものではありません。"));
        }
        $this->request->allowMethod('post', 'delete');
        $this->Post->delete();
        $this->Pnotify->outSuccess(__d('gl', "投稿を削除しました。"));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * post_edit method
     *
     * @throws NotFoundException
     *
     * @param string $id
     *
     * @return void
     */
    public function post_edit($id)
    {
        $this->request->allowMethod('post');
        $this->Post->id = $id;
        if (!$this->Post->exists()) {
            throw new NotFoundException(__('gl', "この投稿は存在しません。"));
        }
        if (!$this->Post->isOwner($this->Auth->user('id'))) {
            throw new NotFoundException(__('gl', "この投稿はあなたのものではありません。"));
        }
        if (isset($this->request->data['Post']['body']) && !empty($this->request->data['Post']['body'])) {
            $this->request->data['Post']['site_info'] = null;
            $ogp = $this->Ogp->getOgpByUrlInText($this->request->data['Post']['body']);
            if (isset($ogp['title']) && isset($ogp['description'])) {
                $this->request->data['Post']['site_info'] = json_encode($ogp);
            }
        }
        if ($this->Post->postEdit($this->request->data)) {
            $this->Pnotify->outSuccess(__d('gl', "投稿の変更を保存しました。"));
        }
        else {
            $error_msg = array_shift($this->Post->validationErrors);
            $this->Pnotify->outError($error_msg[0], ['title' => __d('gl', "投稿の変更に失敗しました。")]);
        }
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * comment_delete method
     *
     * @throws NotFoundException
     *
     * @param string $comment_id
     *
     * @return void
     */
    public function comment_delete($comment_id)
    {
        $this->Post->Comment->id = $comment_id;
        $post_id = $this->Post->Comment->field('post_id');
        if (!$this->Post->Comment->exists()) {
            throw new NotFoundException(__('gl', "このコメントは存在しません。"));
        }
        if (!$this->Post->Comment->isOwner($this->Auth->user('id')) && !$this->User->TeamMember->myStatusWithTeam['TeamMember']['admin_flg']) {
            throw new NotFoundException(__('gl', "このコメントはあなたのものではありません。"));
        }
        $this->request->allowMethod('post', 'delete');
        $this->Post->Comment->delete();
        $this->Post->Comment->updateCounterCache(['post_id' => $post_id]);

        $this->Pnotify->outSuccess(__d('gl', "コメントを削除しました。"));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * comment_edit method
     *
     * @param $comment_id
     *
     * @throws NotFoundException
     * @return void
     */
    public function comment_edit($comment_id)
    {
        $this->Post->Comment->id = $comment_id;
        if (!$this->Post->Comment->exists()) {
            throw new NotFoundException(__('gl', "このコメントは存在しません。"));
        }
        if (!$this->Post->Comment->isOwner($this->Auth->user('id'))) {
            throw new NotFoundException(__('gl', "このコメントはあなたのものではありません。"));
        }
        $this->request->allowMethod('post');
        if (isset($this->request->data['Comment']['body']) && !empty($this->request->data['Comment']['body'])) {
            $this->request->data['Comment']['site_info'] = null;
            $ogp = $this->Ogp->getOgpByUrlInText($this->request->data['Comment']['body']);
            if (isset($ogp['title']) && isset($ogp['description'])) {
                $this->request->data['Comment']['site_info'] = json_encode($ogp);
            }
        }
        if ($this->Post->Comment->commentEdit($this->request->data)) {
            $this->Pnotify->outSuccess(__d('gl', "コメントの変更を保存しました。"));
        }
        else {
            $error_msg = array_shift($this->Post->Comment->validationErrors);
            $this->Pnotify->outError($error_msg[0], ['title' => __d('gl', "コメントの変更に失敗しました。")]);
        }
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function ajax_get_feed()
    {
        $param_named = $this->request->params['named'];
        $this->_ajaxPreProcess();
        if (isset($param_named['page']) && !empty($param_named['page'])) {
            $page_num = $param_named['page'];
        }
        else {
            $page_num = 1;
        }
        $posts = $this->Post->get($page_num, 20, null, null, $this->request->params);
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

    public function ajax_get_post_red_users($post_id)
    {
        $this->_ajaxPreProcess();
        $red_users = $this->Post->PostRead->getRedUsers($post_id);
        $this->set(compact('red_users'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/modal_post_red_users');
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

    public function ajax_get_comment_red_users($comment_id)
    {
        $this->_ajaxPreProcess();
        $red_users = $this->Post->Comment->CommentRead->getRedUsers($comment_id);
        $this->set(compact('red_users'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/modal_comment_red_users');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function comment_add()
    {
        $this->request->allowMethod('post');
        $this->Post->id = isset($this->request->data['Comment']['post_id']) ? $this->request->data['Comment']['post_id'] : null;
        try {
            if (!$this->Post->exists()) {
                throw new RuntimeException(__d('gl', "この投稿は削除されています。"));
            }

            if (isset($this->request->data['Comment']['body']) && !empty($this->request->data['Comment']['body'])) {
                $ogp = $this->Ogp->getOgpByUrlInText($this->request->data['Comment']['body']);
                if (isset($ogp['title']) && isset($ogp['description'])) {
                    $this->request->data['Comment']['site_info'] = json_encode($ogp);
                }
            }
            if ($this->Post->Comment->add($this->request->data)) {
                $this->Pnotify->outSuccess(__d('gl', "コメントしました。"));
            }
            else {
                if (!empty($this->Post->Comment->validationErrors)) {
                    $error_msg = array_shift($this->Post->Comment->validationErrors);
                    throw new RuntimeException($error_msg[0]);
                }
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage(), ['title' => __d('gl', "コメントに失敗しました。")]);
        }
        $this->redirect($this->referer());
    }

}