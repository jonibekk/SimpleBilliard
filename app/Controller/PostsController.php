<?php
App::uses('AppController', 'Controller');

/**
 * Posts Controller
 *
 * @property Post               $Post
 */
class PostsController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Security->unlockedActions = ['ajax_add_comment'];
    }

    /**
     * add method
     *
     * @throws RuntimeException
     * @return void
     */
    public function add()
    {
        $this->request->allowMethod('post');

        // ogbをインサートデータに追加
        $this->request->data['Post'] = $this->_addOgpIndexes(viaIsSet($this->request->data['Post']),
                                                             viaIsSet($this->request->data['Post']['body']));

        // 投稿を保存
        $successSavedPost = $this->Post->addNormal($this->request->data);

        // 保存に失敗
        if (!$successSavedPost) {
            // バリデーションエラーのケース
            if (!empty($this->Post->validationErrors)) {
                $error_msg = array_shift($this->Post->validationErrors);
                $this->Pnotify->outError($error_msg[0], ['title' => __d('gl', "投稿に失敗しました。")]);
            }
            else {
                $this->Pnotify->outError(__d('gl', "投稿に失敗しました。"));
            }
            $this->redirect($this->referer());
        }

        $this->NotifyBiz->execSendNotify(Notification::TYPE_FEED_POST, $this->Post->getLastInsertID());

        $socketId = viaIsSet($this->request->data['socket_id']);
        $share = explode(",", viaIsSet($this->request->data['Post']['share']));

        // リクエストデータが正しくないケース
        if (!$socketId || $share[0] === "") {
            $this->redirect($this->referer());
            $this->Pnotify->outSuccess(__d('gl', "投稿しました。"));
            $this->redirect($this->referer());
        }

        // チーム全体公開が含まれている場合はチーム全体にのみpush
        if (in_array("public", $share)) {
            $this->NotifyBiz->push($socketId, "public");
        }
        else {
            // それ以外の場合は共有先の数だけ回す
            foreach ($share as $val) {
                $this->NotifyBiz->push($socketId, $val);
            }
        }

        $this->Pnotify->outSuccess(__d('gl', "投稿しました。"));

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

        // 例外チェック
        if (!$this->Post->exists()) {
            throw new NotFoundException(__('gl', "この投稿は存在しません。"));
        }
        if (!$this->Post->isOwner($this->Auth->user('id'))) {
            throw new NotFoundException(__('gl', "この投稿はあなたのものではありません。"));
        }

        // ogbをインサートデータに追加
        $this->request->data['Post'] = $this->_addOgpIndexes(viaIsSet($this->request->data['Post']),
                                                             viaIsSet($this->request->data['Post']['body']));

        // 投稿を保存
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
        $this->request->allowMethod('post');
        $this->Post->Comment->id = $comment_id;

        // 例外チェック
        if (!$this->Post->Comment->exists()) {
            throw new NotFoundException(__('gl', "このコメントは存在しません。"));
        }
        if (!$this->Post->Comment->isOwner($this->Auth->user('id'))) {
            throw new NotFoundException(__('gl', "このコメントはあなたのものではありません。"));
        }

        // ogbをインサートデータに追加
        $this->request->data['Comment'] = $this->_addOgpIndexes(viaIsSet($this->request->data['Comment']),
                                                                viaIsSet($this->request->data['Comment']['body']));

        // コメントを追加
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
        $start = null;
        $end = null;
        //一ヶ月以前を指定された場合
        if (isset($param_named['month_index']) && !empty($param_named['month_index'])) {
            $end_month_offset = $param_named['month_index'];
            $start_month_offset = $end_month_offset + 1;
            $end = strtotime("-{$end_month_offset} months", REQUEST_TIMESTAMP);
            $start = strtotime("-{$start_month_offset} months", REQUEST_TIMESTAMP);
        }
        $posts = $this->Post->get($page_num, 20, $start, $end, $this->request->params);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/posts');
        $html = $response->__toString();
        $result = array(
            'html'  => $html,
            'count' => count($posts),
        );
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_action_list_more()
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
        $response = $this->render('Feed/action_posts');
        $html = $response->__toString();
        $result = array(
            'html'  => $html,
            'count' => count($posts),
        );
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_goal_action_feed()
    {
        $this->_ajaxPreProcess();
        $this->_setFeedMoreReadUrl('posts', 'ajax_get_action_list_more');
        $posts = $this->Post->get(1, 20, null, null, $this->request->params);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_action_list');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
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

    public function ajax_get_latest_comment($post_id, $last_comment_id = 0)
    {
        $this->_ajaxPreProcess();
        $comments = $this->Post->Comment->getLatestPostsComment($post_id, $last_comment_id);
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

    public function ajax_get_new_comment_form($post_id)
    {
        $result = [
            'error' => false,
            'msg'   => null,
            'html'  => null
        ];
        $this->_ajaxPreProcess();
        if ($this->Post->isBelongCurrentTeam($post_id)) {
            $this->set(compact('post_id'));
            $response = $this->render('Feed/new_comment_form');
            $html = $response->__toString();
            $result['html'] = $html;
        }
        else {
            $result['error'] = true;
            $result['msg'] = __d('gl', "エラーが発生しました。");
        }
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_edit_comment_form($comment_id)
    {
        $result = [
            'error' => false,
            'msg'   => null,
            'html'  => null
        ];
        $this->_ajaxPreProcess();
        if ($this->Post->Comment->isOwner($this->Auth->user('id'), $comment_id)) {
            $comment = $this->Post->Comment->findById($comment_id);
            $this->set('comment', $comment['Comment']);
            $response = $this->render('Feed/comment_edit_form');
            $html = $response->__toString();
            $result['html'] = $html;
        }
        else {
            $result['error'] = true;
            $result['msg'] = __d('gl', "エラーが発生しました。");
        }
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_edit_post_form($post_id)
    {
        $result = [
            'error' => false,
            'msg'   => null,
            'html'  => null
        ];
        $this->_ajaxPreProcess();
        if ($this->Post->isOwner($this->Auth->user('id'), $post_id)) {
            $post = $this->Post->findById($post_id);
            $this->set(compact('post'));
            $response = $this->render('Feed/post_edit_form');
            $html = $response->__toString();
            $result['html'] = $html;
        }
        else {
            $result['error'] = true;
            $result['msg'] = __d('gl', "エラーが発生しました。");
        }
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

    public function ajax_add_comment()
    {
        $this->request->allowMethod('post');
        $this->_ajaxPreProcess();
        $result = [
            'error' => false,
            'msg'   => ""
        ];
        $this->Post->id = viaIsSet($this->request->data['Comment']['post_id']);
        try {
            if (!$this->Post->exists()) {
                throw new RuntimeException(__d('gl', "この投稿は削除されています。"));
            }

            // ogbをインサートデータに追加
            $this->request->data['Comment'] = $this->_addOgpIndexes(viaIsSet($this->request->data['Comment']),
                                                                    viaIsSet($this->request->data['Comment']['body']));

            // コメントを追加
            if ($this->Post->Comment->add($this->request->data)) {
                $this->NotifyBiz->execSendNotify(Notification::TYPE_FEED_COMMENTED_ON_MY_POST, $this->Post->id,
                                                 $this->Post->Comment->id);
                $this->NotifyBiz->execSendNotify(Notification::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST,
                                                 $this->Post->id, $this->Post->Comment->id);
                $result['msg'] = __d('gl', "コメントしました。");
            }
            else {
                if (!empty($this->Post->Comment->validationErrors)) {
                    $error_msg = array_shift($this->Post->Comment->validationErrors);
                    throw new RuntimeException($error_msg[0]);
                }
            }
        } catch (RuntimeException $e) {
            $result['error'] = true;
            $result['msg'] = $e->getMessage();
            return $this->_ajaxGetResponse($result);
        }

        $this->_pushCommentToBell();
        $this->_pushCommentToPost($this->Post->id);

        return $this->_ajaxGetResponse($result);
    }

    function feed()
    {
        $this->_setMyCircle();
        $this->_setFeedMoreReadUrl();
        $select2_default = $this->User->getAllUsersCirclesSelect2();
        $feed_filter = null;
        $circle_id = viaIsSet($this->request->params['circle_id']);
        $this->_setViewValOnRightColumn();
        //サークル指定の場合はメンバーリスト取得
        if (isset($this->request->params['circle_id']) && !empty($this->request->params['circle_id'])) {
            $circle_members = $this->User->CircleMember->getMembers($this->request->params['circle_id'], true);
        }
        //抽出条件
        if ($circle_id) {
            $feed_filter = 'circle';
        }
        elseif (isset($this->request->params['named']['filter_goal'])) {
            $feed_filter = 'goal';
        }

        $this->set('avail_sub_menu', true);
        $this->set(compact('feed_filter', 'select2_default', 'circle_members', 'circle_id'));
        try {
            $this->set(['posts' => $this->Post->get(1, 20, null, null, $this->request->params)]);
        } catch (RuntimeException $e) {
            //リファラとリクエストのURLが同じ場合は、メッセージを表示せず、ホームにリダイレクトする
            //サークルページに居て当該サークルから抜けた場合の対応
            $params = $this->request->params;
            unset($params['_Token']);
            if ($this->referer(null, true) == Router::url($params)) {
                $this->redirect('/');
            }
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }
    }

    public function ajax_get_share_circles_users_modal($post_id)
    {
        $this->_ajaxPreProcess();
        /** @noinspection PhpUndefinedMethodInspection */
        $circles = $this->Post->PostShareCircle->getShareCirclesAndMembers($post_id);
        $users = $this->Post->PostShareUser->getShareUsersByPost($post_id);
        $total_share_user_count = $this->_getTotalShareUserCount($circles, $users);
        $this->set(compact('circles', 'users', 'total_share_user_count'));
        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_share_circles_users');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    function _getTotalShareUserCount($circles, $users)
    {
        $all_share_user_list = null;
        if (!empty($circles)) {
            foreach ($circles as $k => $v) {
                if (!empty($v['CircleMember'])) {
                    foreach ($v['CircleMember'] as $cm) {
                        if (isset($cm['User']['id'])) {
                            $all_share_user_list[$cm['User']['id']] = $cm['User']['id'];
                        }
                    }
                }
            }
        }
        if (!empty($users)) {
            foreach ($users as $k => $v) {
                if (isset($v['User']['id'])) {
                    $all_share_user_list[$v['User']['id']] = $v['User']['id'];
                }
            }
        }
        $total_share_user_count = count($all_share_user_list);
        return $total_share_user_count;
    }

    /**
     * @param array  $requestData
     * @param string $body
     *
     * @return $requestData
     */
    function _addOgpIndexes($requestData, $body)
    {
        // テキストが空の場合
        if (!$body) {
            return $requestData;
        }

        // ogp取得
        $ogp = $this->Ogp->getOgpByUrlInText($body);

        // ogpが取得できない場合
        $notExistOgp = !isset($ogp['title']) || !isset($ogp['description']);
        if ($notExistOgp) {
            $requestData['site_info'] = null;
            $requestData['site_photo'] = null;
            return $requestData;
        }

        // ogpが取得できた場合
        $requestData['site_info'] = json_encode($ogp);
        if (isset($ogp['image'])) {
            $requestData['site_photo'] = $ogp['image'];
        }
        return $requestData;
    }

    function _pushCommentToBell()
    {
        //push通知するユーザーを定義
        $pushUserList = $this->Post->Comment->getCommentedUniqueUsersList($this->Post->id);
        $findRes = $this->Post->findById($this->Post->id, array('user_id'));
        $postUserId = viaIsSet($findRes['Post']['user_id']);
        if ($postUserId && !in_array($postUserId,
                                     $pushUserList) && $postUserId !== $this->Session->read('Auth.User.id')
        ) {
            $pushUserList[] = $postUserId;
        }

        // pusherに渡すデータを定義
        $teamId = $this->Session->read('current_team_id');
        $socketId = viaIsSet($this->request->data['socket_id']);
        $comment = viaIsSet($this->request->data['Comment']['body']);
        if (!$socketId || !$comment) {
            return;
        }
        // 通知テンプレートのレンダリング
        $view = new View();
        $userName = $this->Session->read('Auth.User.last_name') . $this->Session->read('Auth.User.first_name');
        $postUrl = "/post_permanent/" . $this->Post->id;
        $html = $view->element('bell_notification_item', compact('userName', 'comment', 'postUrl'));
        $notifyId = Security::hash(time());
        $data = array(
            'notify_id'      => $notifyId,
            'is_bell_notify' => true,
            'html'           => $html,
        );

        // Pusherへ送信
        foreach ($pushUserList as $user) {
            $channelName = "user_" . $user . "_team_" . $teamId;
            $this->NotifyBiz->bellPush($socketId, $channelName, $data);
        }
    }

    public function _pushCommentToPost($postId)
    {
        $socketId = viaIsSet($this->request->data['socket_id']);
        $notifyId = Security::hash(time());

        // リクエストデータが正しくないケース
        if (!$socketId) {
            return;
        }

        $data = [
            'notify_id'         => $notifyId,
            'is_comment_notify' => true,
            'post_id'           => $postId
        ];
        $this->NotifyBiz->commentPush($socketId, $data);
    }

}
