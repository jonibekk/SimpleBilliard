<?php
App::uses('AppController', 'Controller');

/**
 * Posts Controller
 *
 * @property Post $Post
 */
class PostsController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function message()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    public function message_list()
    {
        $this->_setMyCircle();
        $this->_setViewValOnRightColumn();
        return $this->render();
    }

    public function ajax_get_message_list()
    {
        $this->_ajaxPreProcess();
        $result = $this->Post->getMessageList();
        $message_list = $this->Post->convertData($result);
        return $this->_ajaxGetResponse($message_list);
    }

    /**
     * add message method
     */
    public function addMessage()
    {
        $this->request->data['Post']['type'] = Post::TYPE_MESSAGE;
        $this->addPost();
        $to_url = Router::url(['controller' => 'posts', 'action' => 'message#', $this->Post->getLastInsertID()], true);
        $this->redirect($to_url);
    }

    public function add()
    {
        $this->addPost();
        $this->redirect($this->_getRedirectUrl());
    }

    /**
     * add method
     *
     * @throws RuntimeException
     * @return void
     */
    public function addPost()
    {
        $this->request->allowMethod('post');

        // ogbをインサートデータに追加
        $this->request->data['Post'] = $this->_addOgpIndexes(viaIsSet($this->request->data['Post']),
                                                             viaIsSet($this->request->data['Post']['body']));

        // 公開投稿か秘密サークルへの投稿かを判別
        if (isset($this->request->data['Post']['share_range'])) {
            $this->request->data['Post']['share'] = ($this->request->data['Post']['share_range'] == 'public')
                ? $this->request->data['Post']['share_public']
                : $this->request->data['Post']['share_secret'];
        }

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
            return false;
        }

        $notify_type = NotifySetting::TYPE_FEED_POST;
        if (viaIsSet($this->request->data['Post']['type']) == Post::TYPE_MESSAGE) {
            $notify_type = NotifySetting::TYPE_FEED_MESSAGE;
        }
        $this->NotifyBiz->execSendNotify($notify_type, $this->Post->getLastInsertID());

        $socketId = viaIsSet($this->request->data['socket_id']);
        $share = explode(",", viaIsSet($this->request->data['Post']['share']));

        // リクエストデータが正しくないケース
        if (!$socketId || $share[0] === "") {
            $this->Pnotify->outSuccess(__d('gl', "投稿しました。"));
            return false;
        }

        $mixpanel_prop_name = null;
        // チーム全体公開が含まれている場合はチーム全体にのみpush
        if (in_array("public", $share)) {
            $this->NotifyBiz->push($socketId, "public");
            $mixpanel_prop_name = MixpanelComponent::PROP_SHARE_TEAM;
        }
        else {
            $share_circle = false;
            // それ以外の場合は共有先の数だけ回す
            foreach ($share as $val) {
                if (strpos($val, "circle") !== false) {
                    $share_circle = true;
                }
                $this->NotifyBiz->push($socketId, $val);
            }
            if ($share_circle) {
                $mixpanel_prop_name = MixpanelComponent::PROP_SHARE_CIRCLE;
            }
            else {
                $mixpanel_prop_name = MixpanelComponent::PROP_SHARE_MEMBERS;
            }
        }
        $this->Mixpanel->trackPost($mixpanel_prop_name, $this->Post->getLastInsertID());

        $this->Pnotify->outSuccess(__d('gl', "投稿しました。"));
        return true;

    }

    /**
     * post_delete method
     *
     * @throws NotFoundException
     * @return void
     */
    public function post_delete()
    {
        $this->Post->id = viaIsSet($this->request->params['named']['post_id']);
        if (!$this->Post->exists()) {
            throw new NotFoundException(__d('gl', "この投稿は存在しません。"));
        }
        if (!$this->Post->isOwner($this->Auth->user('id')) && !$this->User->TeamMember->myStatusWithTeam['TeamMember']['admin_flg']) {
            throw new NotFoundException(__d('gl', "この投稿はあなたのものではありません。"));
        }
        $this->request->allowMethod('post', 'delete');
        $this->Post->delete();
        $this->Post->PostFile->AttachedFile->deleteAllRelatedFiles($this->Post->id,
                                                                   AttachedFile::TYPE_MODEL_POST);
        $this->Pnotify->outSuccess(__d('gl', "投稿を削除しました。"));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * post_edit method
     *
     * @throws NotFoundException
     * @return void
     */
    public function post_edit()
    {
        $this->Post->id = viaIsSet($this->request->params['named']['post_id']);

        // 例外チェック
        if (!$this->Post->exists()) {
            throw new NotFoundException(__d('gl', "この投稿は存在しません。"));
        }
        if (!$this->Post->isOwner($this->Auth->user('id'))) {
            throw new NotFoundException(__d('gl', "この投稿はあなたのものではありません。"));
        }

        // フォームが submit された時
        if ($this->request->is('put')) {
            $this->request->data['Post']['id'] = $this->request->params['named']['post_id'];
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
            return $this->redirect(
                ['controller' => 'posts',
                 'action'     => 'feed',
                 'post_id'    => $this->request->params['named']['post_id']]);
        }

        // 編集フォーム表示
        $this->_setViewValOnRightColumn();
        $this->set('common_form_type', 'post');
        $this->set('common_form_mode', 'edit');
        $rows = $this->Post->get(1, 1, null, null,
                                 ['named' => ['post_id' => $this->request->params['named']['post_id']]]);
        $this->request->data = $rows[0];
        $this->layout = LAYOUT_ONE_COLUMN;
    }

    /**
     * comment_delete method
     *
     * @throws NotFoundException
     * @return void
     */
    public function comment_delete()
    {
        $this->Post->Comment->id = viaIsSet($this->request->params['named']['comment_id']);;
        $post_id = $this->Post->Comment->field('post_id');
        if (!$this->Post->Comment->exists()) {
            throw new NotFoundException(__('gl', "このコメントは存在しません。"));
        }
        if (!$this->Post->Comment->isOwner($this->Auth->user('id')) && !$this->User->TeamMember->myStatusWithTeam['TeamMember']['admin_flg']) {
            throw new NotFoundException(__('gl', "このコメントはあなたのものではありません。"));
        }
        $this->request->allowMethod('post', 'delete');
        $this->Post->Comment->delete();
        $this->Post->PostFile->AttachedFile->deleteAllRelatedFiles($this->Post->Comment->id,
                                                                   AttachedFile::TYPE_MODEL_COMMENT);
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
    public function comment_edit()
    {
        $this->request->allowMethod('post');
        $this->Post->Comment->id = viaIsSet($this->request->params['named']['comment_id']);;

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
        if (isset($this->request->params['named']['circle_id'])
        ) {
            $this->set('long_text', true);
        }
        else {
            $this->set('long_text', false);
        }

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
        $posts = $this->Post->get($page_num, POST_FEED_PAGE_ITEMS_NUMBER, $start, $end, $this->request->params,false);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/posts');
        $html = $response->__toString();
        $result = array(
            'html'          => $html,
            'count'         => count($posts),
            'page_item_num' => POST_FEED_PAGE_ITEMS_NUMBER,
            'start'         => $start ? $start : REQUEST_TIMESTAMP - MONTH,
        );
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_message_info($post_id)
    {
        $this->_ajaxPreProcess();

        $room_info = $this->Post->getPostById($post_id);
        $room_info['User']['photo_path'] = $this->Post->getPhotoPath($room_info['User']);

        $res = [
            'auth_info' => [
                'photo_path' => $this->Post->getPhotoPath($this->Auth->user()),
            ],
            'room_info' => $room_info
        ];

        //対象のメッセージルーム(Post)のnotifyがあれば削除する
        //メッセージなら該当するnotifyをredisから削除する
        //なお通知は1ルームあたりからなず1個のため、notify_id = post_id
        $this->NotifyBiz->removeMessageNotification($post_id);

        return $this->_ajaxGetResponse($res);
    }

    public function ajax_get_message($post_id, $limit, $page_num)
    {
        $this->_ajaxPreProcess();
        $message_list = $this->Post->Comment->getPostsComment($post_id, $limit, $page_num, 'desc');
        $convert_msg_data = $this->Post->Comment->convertData($message_list);
        $result = ['message_list' => $convert_msg_data];
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_put_message($post_id, $message) {
        $this->_ajaxPreProcess();

        $params['Comment']['post_id'] = $post_id;
        $params['Comment']['body'] = $message;
        $comment_id = $this->Post->Comment->add($params);
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_MESSAGE, $post_id, $comment_id);

        $detail_comment = $this->Post->Comment->getComment($comment_id);
        $convert_data = $this->Post->Comment->convertData($detail_comment);

        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $pusher->trigger('message-channel-'. $post_id, 'new_message', $convert_data);

        return $this->_ajaxGetResponse($detail_comment);
    }

    public function ajax_put_message_read($post_id, $comment_id)
    {
        $this->_ajaxPreProcess();
        $res = $this->Post->Comment->CommentRead->red([$comment_id]);
        if ($res === true) {
            $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
            $pusher->trigger('message-channel-'. $post_id, 'read_message', $comment_id);
        }
        return $this->_ajaxGetResponse($res);
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
        $posts = $this->Post->get($page_num, POST_FEED_PAGE_ITEMS_NUMBER, null, null, $this->request->params);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/action_posts');
        $html = $response->__toString();
        $result = array(
            'html'          => $html,
            'count'         => count($posts),
            'page_item_num' => POST_FEED_PAGE_ITEMS_NUMBER,
            'start'         => 0,
        );
        return $this->_ajaxGetResponse($result);
    }

    /**
     * ユーザーページの投稿一覧読み込み ajax
     *
     * @return CakeResponse
     */
    public function ajax_get_user_page_post_feed()
    {
        $param_named = $this->request->params['named'];
        $this->_ajaxPreProcess();

        // 表示するページ
        $page_num = 1;
        if (isset($param_named['page']) && !empty($param_named['page'])) {
            $page_num = $param_named['page'];
        }
        // データ取得期間
        $start = null;
        $end = null;
        if (isset($param_named['month_index']) && !empty($param_named['month_index'])) {
            // 一ヶ月以前を指定された場合
            $end_month_offset = $param_named['month_index'];
            $start_month_offset = $end_month_offset + 1;
            $end = strtotime("-{$end_month_offset} months", REQUEST_TIMESTAMP);
            $start = strtotime("-{$start_month_offset} months", REQUEST_TIMESTAMP);
        }
        //取得件数
        $item_num = POST_FEED_PAGE_ITEMS_NUMBER;
        //エレメントpath
        $elm_path = "Feed/posts";
        if (viaIsSet($param_named['page_type']) == 'image') {
            $item_num = MY_PAGE_CUBE_ACTION_IMG_NUMBER;
            $elm_path = "cube_img_blocks";
        }
        // 投稿一覧取得
        $posts = $this->Post->get($page_num, $item_num, $start, $end, $this->request->params);
        $this->set('posts', $posts);
        $this->set('long_text', false);
        $without_header = viaIsSet($param_named['without_header']);
        $this->set(compact('without_header'));

        // エレメントの出力を変数に格納する
        // htmlレンダリング結果
        $response = $this->render($elm_path);
        $html = $response->__toString();
        $result = array(
            'html'          => $html,
            'count'         => count($posts),
            'page_item_num' => $item_num,
            'start'         => $start ? $start : REQUEST_TIMESTAMP - MONTH,
        );
        return $this->_ajaxGetResponse($result);
    }

    /**
     * サークルのファイル一覧読み込み ajax
     *
     * @return CakeResponse
     */
    public function ajax_get_circle_files()
    {
        $param_named = $this->request->params['named'];
        $this->_ajaxPreProcess();

        // 表示するページ
        $page_num = 1;
        if (isset($param_named['page']) && !empty($param_named['page'])) {
            $page_num = $param_named['page'];
        }
        // データ取得期間
        $start = null;
        $end = null;
        if (isset($param_named['month_index']) && !empty($param_named['month_index'])) {
            // 一ヶ月以前を指定された場合
            $end_month_offset = $param_named['month_index'];
            $start_month_offset = $end_month_offset + 1;
            $end = strtotime("-{$end_month_offset} months", REQUEST_TIMESTAMP);
            $start = strtotime("-{$start_month_offset} months", REQUEST_TIMESTAMP);
        }
        //取得件数
        $item_num = FILE_LIST_PAGE_NUMBER;
        //ファイル一覧取得
        $files = $this->Post->getFilesOnCircle($param_named['circle_id'],
                                               $page_num, $item_num, $start, $end,
                                               viaIsSet($param_named['file_type']));
        $this->set('files', $files);
        // エレメントの出力を変数に格納する
        // htmlレンダリング結果
        $response = $this->render('Feed/attached_files');
        $html = $response->__toString();
        $result = array(
            'html'          => $html,
            'count'         => count($files),
            'page_item_num' => $item_num,
            'start'         => $start ? $start : REQUEST_TIMESTAMP - MONTH,
        );
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_goal_action_feed()
    {
        $this->_ajaxPreProcess();
        $this->_setFeedMoreReadUrl('posts', 'ajax_get_action_list_more');
        $posts = $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null, $this->request->params);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_action_list');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_old_comment($get_num)
    {
        $post_id = viaIsSet($this->request->params['named']['post_id']);
        $this->_ajaxPreProcess();
        $comments = $this->Post->Comment->getPostsComment($post_id, $get_num);
        $long_text = false;
        if (isset($this->request->params['named']['long_text'])) {
            $long_text = $this->request->params['named']['long_text'];
        }
        $this->set('long_text', $long_text);
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

    public function ajax_get_latest_comment($last_comment_id = 0)
    {
        $post_id = viaIsSet($this->request->params['named']['post_id']);
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

    public function ajax_get_new_comment_form($prefix = null)
    {
        $post_id = viaIsSet($this->request->params['named']['post_id']);
        $result = [
            'error' => false,
            'msg'   => null,
            'html'  => null
        ];
        $this->_ajaxPreProcess();
        if ($this->Post->isBelongCurrentTeam($post_id, $this->Session->read('current_team_id'))) {
            $this->set(compact('post_id', 'prefix'));
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

    public function ajax_get_edit_comment_form($id_prefix = null)
    {
        $comment_id = viaIsSet($this->request->params['named']['comment_id']);
        $result = [
            'error' => false,
            'msg'   => null,
            'html'  => null
        ];
        $this->_ajaxPreProcess();
        if ($this->Post->Comment->isOwner($this->Auth->user('id'), $comment_id)) {
            $comment = $this->Post->Comment->findById($comment_id);
            $this->set('comment', $comment['Comment']);
            $this->set('id_prefix', $id_prefix);
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

    public function ajax_get_edit_post_form()
    {
        $post_id = viaIsSet($this->request->params['named']['post_id']);
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

    public function ajax_post_like()
    {
        $post_id = viaIsSet($this->request->params['named']['post_id']);
        $this->_ajaxPreProcess();
        $res = $this->Post->PostLike->changeLike($post_id);
        if ($res['is_liked']) {
            $post = $this->Post->findById($post_id);
            $type = viaIsSet($post['Post']['type']);
            $this->Mixpanel->trackLike($type);
        }
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_comment_like()
    {
        $comment_id = viaIsSet($this->request->params['named']['comment_id']);
        $this->_ajaxPreProcess();
        $res = $this->Post->Comment->CommentLike->changeLike($comment_id);
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_get_post_liked_users()
    {
        $post_id = viaIsSet($this->request->params['named']['post_id']);
        $this->_ajaxPreProcess();
        $liked_users = $this->Post->PostLike->getLikedUsers($post_id);
        $this->set(compact('liked_users'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/modal_post_liked_users');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_post_red_users()
    {
        $post_id = viaIsSet($this->request->params['named']['post_id']);
        $this->_ajaxPreProcess();
        $red_users = $this->Post->PostRead->getRedUsers($post_id);
        $this->set(compact('red_users'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/modal_post_red_users');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_comment_liked_users()
    {
        $comment_id = viaIsSet($this->request->params['named']['comment_id']);
        $this->_ajaxPreProcess();
        $liked_users = $this->Post->Comment->CommentLike->getLikedUsers($comment_id);
        $this->set(compact('liked_users'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/modal_comment_liked_users');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_comment_red_users()
    {
        $comment_id = viaIsSet($this->request->params['named']['comment_id']);
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
        $post = $this->Post->findById($this->Post->id);
        $type = viaIsSet($post['Post']['type']);
        try {
            if (!$this->Post->exists()) {
                throw new RuntimeException(__d('gl', "この投稿は削除されています。"));
            }

            // ogbをインサートデータに追加
            $this->request->data['Comment'] = $this->_addOgpIndexes(viaIsSet($this->request->data['Comment']),
                                                                    viaIsSet($this->request->data['Comment']['body']));

            // コメントを追加
            if ($this->Post->Comment->add($this->request->data)) {
                switch ($type) {
                    case Post::TYPE_NORMAL:
                        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST, $this->Post->id,
                                                         $this->Post->Comment->id);
                        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST,
                                                         $this->Post->id, $this->Post->Comment->id);
                        break;
                    case Post::TYPE_ACTION:
                        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_ACTION,
                                                         $this->Post->id,
                                                         $this->Post->Comment->id);
                        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION,
                                                         $this->Post->id, $this->Post->Comment->id);
                        break;
                    case Post::TYPE_MESSAGE:
                        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_MESSAGE, $this->Post->id,
                                                         $this->Post->Comment->id);
                        break;
                }
                //mixpanel
                $this->Mixpanel->trackComment($type);

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

        $this->_pushCommentToPost($this->Post->id, time());

        return $this->_ajaxGetResponse($result);
    }

    function feed()
    {
        $this->_setCircleCommonVariables();
        $this->_setViewValOnRightColumn();

        try {
            $this->set(['posts' => $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null,
                                                    $this->request->params)]);
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

    public function attached_file_list()
    {
        $this->_setCircleCommonVariables();
        $this->_setViewValOnRightColumn();
        $circle_id = $this->_getRequiredParam('circle_id');
        $file_type_options = $this->Post->PostFile->AttachedFile->getFileTypeOptions();
        $files = $this->Post->getFilesOnCircle($circle_id, 1, FILE_LIST_PAGE_NUMBER, null, null,
                                               viaIsSet($this->request->params['named']['file_type']));

        $circle_file_list_base_url = Router::url(
            [
                'controller' => 'posts', 'action' => 'attached_file_list', 'circle_id' => $circle_id
            ]);

        $this->set(compact('files', 'file_type_options', 'circle_file_list_base_url'));
        return $this->render();
    }

    function _setCircleCommonVariables()
    {
        $params = $this->request->params;
        $params = array_merge($params, $params['named']);
        $this->_setMyCircle();
        $this->_setCurrentCircle();
        $this->_setFeedMoreReadUrl();

        if (isset($params['circle_id']) ||
            isset($params['post_id'])
        ) {
            $this->set('long_text', true);
        }
        else {
            $this->set('long_text', false);
        }

        $feed_filter = null;
        $circle_id = viaIsSet($params['circle_id']);
        $user_status = $this->_userCircleStatus($params['circle_id']);

        $circle_status = $this->Post->Circle->CircleMember->show_hide_stats($this->Auth->user('id'),
                                                                            $params['circle_id']);

        //サークル指定の場合はメンバーリスト取得
        if (isset($params['circle_id']) && !empty($params['circle_id'])) {
            $circle_member_count = $this->User->CircleMember->getActiveMemberCount($params['circle_id']);
        }
        //抽出条件
        if ($circle_id) {
            $feed_filter = 'circle';
        }
        elseif (isset($params['filter_goal'])) {
            $feed_filter = 'goal';
        }

        $this->set('common_form_type', 'post');
        $this->set(compact('feed_filter', 'circle_member_count', 'circle_id', 'user_status', 'params',
                           'circle_status'));

        try {
            $targetPosts = $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null, $this->request->params);
            $this->set(['posts' => $targetPosts]);

            //メッセージなら該当するnotifyをredisから削除する
            $post_type = viaIsSet($targetPosts[0]['Post']['type']);
            if ($post_type == Post::TYPE_MESSAGE) {
                $notify_id = viaIsSet($this->request->params['named']['notify_id']);
                $this->NotifyBiz->removeMessageNotification($notify_id);
            }

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

    public function ajax_get_share_circles_users_modal()
    {
        $post_id = viaIsSet($this->request->params['named']['post_id']);
        $this->_ajaxPreProcess();
        /** @noinspection PhpUndefinedMethodInspection */
        $circles = $this->Post->PostShareCircle->getShareCirclesAndMembers($post_id);
        $users = $this->Post->PostShareUser->getShareUsersByPost($post_id);
        $total_share_user_count = $this->_getTotalShareUserCount($circles, $users);
        $this->set(compact('circles', 'users', 'total_share_user_count'));
        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_share_range');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    /**
     * ファイルアップロード
     *
     * @return CakeResponse
     */
    public function ajax_upload_file()
    {
        $this->_ajaxPreProcess();
        $file_id = $this->Post->PostFile->AttachedFile->preUploadFile($this->request->params['form']);
        return $this->_ajaxGetResponse(['error' => $file_id ? false : true,
                                        'msg'   => $file_id ? "" : __d('gl', 'アップロードに失敗しました'),
                                        'id'    => $file_id ? $file_id : "",
                                       ]);
    }

    /**
     * アップロードしたファイルを削除
     *
     * @return CakeResponse
     */
    public function ajax_remove_file()
    {
        $this->_ajaxPreProcess();
        $success = $this->Post->PostFile->AttachedFile->cancelUploadFile($this->request->data('AttachedFile.file_id'));
        return $this->_ajaxGetResponse(['error' => !$success,
                                        'msg'   => $success
                                            ? __d('gl', 'ファイルを削除しました')
                                            : __d('gl', 'ファイルの削除に失敗しました'),
                                        'id'    => "",
                                       ]);
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
     * @return array $requestData
     */
    function _addOgpIndexes($requestData, $body)
    {

        // blank or not string, then return;
        if (!$body || !is_string($body)) {
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

            $extension = pathinfo($ogp['image'], PATHINFO_EXTENSION);

            $allowed_extensions = array("jpg", "jpeg", "png", "gif");
            if (!in_array($extension, $allowed_extensions)) {
                $ogp['image'] = null;
            }
            $requestData['site_photo'] = $ogp['image'];

        }
        return $requestData;
    }

    /**
     * @param $postId
     * @param $date
     */
    public function _pushCommentToPost($postId, $date)
    {
        $socketId = viaIsSet($this->request->data['socket_id']);
        $notifyId = Security::hash($date);

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

    public function join_circle()
    {
        if (!$this->_isAvailCircle()) {
            $this->Pnotify->outError(__d('gl', "アクセスURLに誤りがあります。"));
            return $this->redirect($this->referer());
        }

        if ($this->Post->Circle->CircleMember->joinNewMember($this->request->params['named']['circle_id'])) {
            $this->Pnotify->outSuccess(__d('gl', "You have joined the circle"));
        }
        else {
            $this->Pnotify->outError(__d('gl', "Error in joining the circle"));
        }
        return $this->redirect($this->request->referer());

    }

    public function unjoin_circle()
    {
        if (!$this->_isAvailCircle()) {
            $this->Pnotify->outError(__d('gl', "アクセスURLに誤りがあります。"));
            return $this->redirect($this->referer());
        }
        $circle_id = $this->request->params['named']['circle_id'];
        if ($circle_id == $this->Post->Circle->getTeamAllCircleId()) {
            $this->Pnotify->outError(__d('gl', "このサークルから抜けることはできません。"));
            return $this->redirect($this->referer());
        }
        $this->Post->Circle->CircleMember->unjoinMember($circle_id);
        $this->Pnotify->outSuccess(__d('gl', "You have successfully left the circle"));
        return $this->redirect($this->referer());
    }

    public function _isAvailCircle()
    {
        if (!isset($this->request->params['named']['circle_id'])) {
            return false;
        }
        $circle_id = $this->request->params['named']['circle_id'];
        if (!$this->Post->Circle->isBelongCurrentTeam($circle_id, $this->current_team_id)) {
            return false;
        }
        return true;
    }

    public function _userCircleStatus($circle_id)
    {
        if ($this->Post->Circle->CircleMember->isAdmin($this->Auth->user('id'), $circle_id)) {
            return 'admin';
        }
        else {
            if ($this->Post->Circle->CircleMember->isBelong($circle_id, $this->Auth->user('id'))) {
                return 'joined';
            }
        }
        return 'not_joined';
    }

    function circle_toggle_status($status)
    {
        $circle_id = $this->request->params['named']['circle_id'];
        $this->Post->Circle->CircleMember->set(['show_for_all_feed_flg' => $status]);

        if ($this->Post->Circle->CircleMember->validates()) {
            $this->Post->Circle->CircleMember->circle_status_toggle($circle_id, $status);
            return $this->redirect($this->request->referer());
        }
        else {
            throw new NotFoundException(__('gl', "Invalid Request"));
        }
    }

}
