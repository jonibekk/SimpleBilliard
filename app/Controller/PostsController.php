<?php
App::uses('AppController', 'Controller');

/**
 * Posts Controller
 *
 * @property Post $Post
 */
class PostsController extends AppController
{
    //　メッセージリストの1ページあたり表示件数
    public static $message_list_page_count = 7;

    public function beforeFilter()
    {
        parent::beforeFilter();
        //angularから渡すPOSTのデータでフォーム改ざんチェック用のハッシュ生成ができない為、ここで改ざんチェックを除外指定
        if ($this->request->params['action'] == 'ajax_put_message') {
            $this->Security->validatePost = false;
        }

        if ($this->request->params['action'] === 'ajax_add_post_for_setup_guide') {
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = false;
        }
    }

    public function message()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->set('without_footer', true);
        return $this->render();
    }

    public function ajax_message()
    {
        $this->_ajaxPreProcess();

        $response = $this->render('/Posts/message');

        $html = $response->__toString();
        $result = array(
            'html' => $html,
        );
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_message_list()
    {
        $this->_ajaxPreProcess();

        $response = $this->render('/Posts/message_list');

        $html = $response->__toString();
        $result = array(
            'html' => $html,
        );
        return $this->_ajaxGetResponse($result);
    }

    public function message_list()
    {
        // 宛先のユーザー情報取得
        $targetUserId = $this->request->query('userId');
        $targetUserId = is_numeric($targetUserId) ? $targetUserId : '';
        $this->set(compact('targetUserId'));

        $this->_setViewValOnRightColumn();
        return $this->render();
    }

    public function ajax_get_message_list($page = 1)
    {
        $this->_ajaxPreProcess();
        $result = $this->Post->getMessageList($this->Auth->user('id'),
            PostsController::$message_list_page_count, $page);
        $message_list = $this->Post->convertData($result);
        $notify_list = $this->_getMessageNotifyPostIdArray();

        if (!empty($notify_list)) {
            foreach ($message_list as $key => $value) {
                $post_id = $value['Post']['id'];
                if (isset($notify_list[$post_id])) {
                    $message_list[$key]['Post']['is_unread'] = true;
                }
            }
        }

        $res = [
            'auth_info'    => [
                'user_id'    => $this->Auth->user('id'),
                'language'   => $this->Auth->user('language'),
                'photo_path' => $this->Post->getPhotoPath($this->Auth->user())
            ],
            'message_list' => $message_list,
        ];
        return $this->_ajaxGetResponse($res);
    }

    /**
     * 未読通知があるメッセージ通知のpostid配列を返す
     *
     * @return array|null
     */
    function _getMessageNotifyPostIdArray()
    {
        $notify_items = $this->NotifyBiz->getMessageNotification();
        if (empty($notify_items)) {
            return null;
        }
        $post_ids = [];
        foreach ($notify_items as $item) {
            $post_id = $item['Notification']['id'];
            $post_ids[$post_id] = true;
        }
        return $post_ids;
    }

    /**
     * add message method
     */
    public function add_message()
    {
        $this->request->data['Post']['type'] = Post::TYPE_MESSAGE;
        $this->_addPost();
        $to_url = Router::url(['controller' => 'posts', 'action' => 'message#', $this->Post->getLastInsertID()], true);
        $this->redirect($to_url);
    }

    /**
     * メッセージのメンバー変更のPostを受け取って処理
     */
    public function edit_message_users()
    {
        $id = $this->request->data['Post']['post_id'];
        $this->request->data['Post']['type'] = Post::TYPE_MESSAGE;
        $this->request->allowMethod('post');
        // 共有範囲を格納
        $this->request->data['Post']['share'] = $this->request->data['Post']['share_public'];
        // メッセージ変更を保存
        $successSavedPost = $this->Post->editMessageMember($this->request->data);

        // 保存に失敗
        if (!$successSavedPost) {
            // バリデーションエラーのケース
            if (!empty($this->Post->validationErrors)) {
                $error_msg = array_shift($this->Post->validationErrors);
                $this->Pnotify->outError($error_msg[0], ['title' => __("Failed to change members list.")]);
            } else {
                $this->Pnotify->outError(__("Failed to change members list."));
            }
        }

        $this->Pnotify->outSuccess(__("Changed members list."));
        $this->redirect(['controller' => 'posts', 'action' => 'message#', $id]);
    }

    public function add()
    {
        $this->_addPost();
        $this->redirect($this->_getRedirectUrl());
    }

    /**
     * add method
     *
     * @throws RuntimeException
     * @return void
     */
    public function _addPost()
    {
        $this->request->allowMethod('post');

        // OGP処理はメッセ、アクション以外の場合に実行
        if ($url_text = $this->request->data('Post.site_info_url')) {
            // OGPをインサートデータに追加
            $this->request->data['Post'] = $this->_addOgpIndexes($this->request->data['Post'], $url_text);
        }

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
                $this->Pnotify->outError($error_msg[0], ['title' => __("Failed to post.")]);
            } else {
                $this->Pnotify->outError(__("Failed to post."));
            }
            return false;
        }

        $this->updateSetupStatusIfNotCompleted();

        $notify_type = NotifySetting::TYPE_FEED_POST;
        if (Hash::get($this->request->data, 'Post.type') == Post::TYPE_MESSAGE) {
            $notify_type = NotifySetting::TYPE_FEED_MESSAGE;
        }
        $this->NotifyBiz->execSendNotify($notify_type, $this->Post->getLastInsertID());

        $socketId = Hash::get($this->request->data, 'socket_id');
        $share = explode(",", Hash::get($this->request->data, 'Post.share'));

        //何らかの原因でsocketIdが無いもしくは、共有先指定なしの場合は以降の処理(通知、イベントトラッキング)を行わない
        if (!$socketId || $share[0] === "") {
            $this->Pnotify->outSuccess(__("Posted."));
            return false;
        }

        $share_circle = false;
        if (Hash::get($this->request->data, 'Post.type') != Post::TYPE_MESSAGE) {
            //push to pusher
            // チーム全体公開が含まれている場合はチーム全体にのみpush
            if (in_array("public", $share)) {
                $this->NotifyBiz->push($socketId, "public");
            } else {
                // それ以外の場合は共有先の数だけ回す
                foreach ($share as $val) {
                    if (strpos($val, "circle") !== false) {
                        $share_circle = true;
                    }
                    $this->NotifyBiz->push($socketId, $val);
                }
            }
        }

        //push for updating circle list
        if (Hash::get($this->request->data, 'Post.type') != Post::TYPE_MESSAGE) {
            $this->NotifyBiz->pushUpdateCircleList($socketId, $share);
        }

        //publish an event to Mixpanel
        $mixpanel_prop_name = null;
        if (Hash::get($this->request->data, 'Post.type') == Post::TYPE_MESSAGE) {
            $this->Mixpanel->trackMessage($this->Post->getLastInsertID());
        } else {
            if (in_array("public", $share)) {
                $mixpanel_prop_name = MixpanelComponent::PROP_SHARE_TEAM;
            } else {
                if ($share_circle) {
                    $mixpanel_prop_name = MixpanelComponent::PROP_SHARE_CIRCLE;
                } else {
                    $mixpanel_prop_name = MixpanelComponent::PROP_SHARE_MEMBERS;
                }
            }
            $this->Mixpanel->trackPost($this->Post->getLastInsertID(), $mixpanel_prop_name);
        }

        $this->Pnotify->outSuccess(__("Posted."));
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
        $this->Post->id = Hash::get($this->request->params, 'named.post_id');
        if (!$this->Post->exists()) {
            throw new NotFoundException(__("This post doesn't exist."));
        }
        if (!$this->Post->isOwner($this->Auth->user('id')) && !$this->User->TeamMember->myStatusWithTeam['TeamMember']['admin_flg']) {
            throw new NotFoundException(__("This isn't your post."));
        }
        $this->request->allowMethod('post', 'delete');
        $this->Post->delete();
        $this->Post->PostFile->AttachedFile->deleteAllRelatedFiles($this->Post->id,
            AttachedFile::TYPE_MODEL_POST);
        $this->Pnotify->outSuccess(__("Deleted the post."));
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
        $this->Post->id = Hash::get($this->request->params, 'named.post_id');

        // 例外チェック
        if (!$this->Post->exists()) {
            throw new NotFoundException(__("This post doesn't exist."));
        }
        if (!$this->Post->isOwner($this->Auth->user('id'))) {
            throw new NotFoundException(__("This isn't your post."));
        }

        // フォームが submit された時
        if ($this->request->is('put')) {
            $this->request->data['Post']['id'] = $this->request->params['named']['post_id'];

            // OGP処理はメッセ、アクション以外の場合に実行
            if ($url_text = $this->request->data('Post.site_info_url')) {
                // OGPをインサートデータに追加
                $this->request->data['Post'] = $this->_addOgpIndexes($this->request->data['Post'], $url_text);
            } else {
                // 編集の場合、OGP情報がフォームから渡ってこないとDBに残ってしまうので
                // 空指定をする必要がある
                $this->request->data['Post']['site_info'] = null;
                $this->request->data['Post']['site_photo_file_name'] = null;
            }

            // 投稿を保存
            if ($this->Post->postEdit($this->request->data)) {
                $this->Pnotify->outSuccess(__("Saved changes."));
            } else {
                $error_msg = array_shift($this->Post->validationErrors);
                $this->Pnotify->outError($error_msg[0], ['title' => __("Failed to save changes.")]);
            }
            /** @noinspection PhpInconsistentReturnPointsInspection */
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect(
                [
                    'controller' => 'posts',
                    'action'     => 'feed',
                    'post_id'    => $this->request->params['named']['post_id']
                ]);
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
        $this->Post->Comment->id = Hash::get($this->request->params, 'named.comment_id');;
        $post_id = $this->Post->Comment->field('post_id');
        if (!$this->Post->Comment->exists()) {
            throw new NotFoundException(__("This comment doesn't exist."));
        }
        if (!$this->Post->Comment->isOwner($this->Auth->user('id')) && !$this->User->TeamMember->myStatusWithTeam['TeamMember']['admin_flg']) {
            throw new NotFoundException(__("This isn't your comment."));
        }
        $this->request->allowMethod('post', 'delete');
        $this->Post->Comment->delete();
        $this->Post->PostFile->AttachedFile->deleteAllRelatedFiles($this->Post->Comment->id,
            AttachedFile::TYPE_MODEL_COMMENT);
        $this->Post->Comment->updateCounterCache(['post_id' => $post_id]);

        $this->Pnotify->outSuccess(__("Deleted the comment."));
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
        $this->Post->Comment->id = Hash::get($this->request->params, 'named.comment_id');

        // 例外チェック
        if (!$this->Post->Comment->exists()) {
            throw new NotFoundException(__("This comment doesn't exist."));
        }
        if (!$this->Post->Comment->isOwner($this->Auth->user('id'))) {
            throw new NotFoundException(__("This isn't your comment."));
        }

        // ogbをインサートデータに追加
        $this->request->data['Comment'] = $this->_addOgpIndexes(Hash::get($this->request->data, 'Comment'),
            Hash::get($this->request->data, 'Comment.body'));

        // コメントを追加
        if ($this->Post->Comment->commentEdit($this->request->data)) {
            $this->Pnotify->outSuccess(__("Edited the comment."));
        } else {
            $error_msg = array_shift($this->Post->Comment->validationErrors);
            $this->Pnotify->outError($error_msg[0], ['title' => __("Failed to save changes to the comment.")]);
        }
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function ajax_get_feed($view = "Feed/posts", $user_status = null, $circle_member_count = 0)
    {
        $param_named = $this->request->params['named'];
        $this->_ajaxPreProcess();

        $notify_id = $this->request->query('notify_id');
        if ($notify_id) {
            $this->set('long_text', true);
        } else {
            $this->set('long_text', false);
        }

        if (isset($param_named['page']) && !empty($param_named['page'])) {
            $page_num = $param_named['page'];
        } else {
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
        $posts = $this->Post->get($page_num, POST_FEED_PAGE_ITEMS_NUMBER, $start, $end, $this->request->params);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        //1.フィードのスクロールによる投稿取得 2.notifyから投稿詳細ページに遷移した場合の投稿取得
        //1,2どちらのケースでもこのコードが実行されるが、「not exist」メッセージを出すのは2のケースのみのため、
        //ここで分岐をする必要がある。
        $is_notify_post_permanent_page = isset($this->request->params['post_id']) && $notify_id;
        if ($is_notify_post_permanent_page && !$posts) {
            $response = $this->render('Feed/post_not_found');
        } else {
            $response = $this->render($view);
        }

        $html = $response->__toString();
        $result = array(
            'html'                => $html,
            'count'               => count($posts),
            'page_item_num'       => POST_FEED_PAGE_ITEMS_NUMBER,
            'start'               => $start ? $start : REQUEST_TIMESTAMP - MONTH,
            'circle_member_count' => $circle_member_count,
            'user_status'         => $user_status,
        );
        if (isset($posts[0]['Post']['modified'])) {
            $result['post_time_before'] = $posts[0]['Post']['modified'];
        }

        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_message_info($post_id)
    {
        $text_ex = new TextExHelper(new View());
        $this->_ajaxPreProcess();
        $room_info = $this->Post->getPostById($post_id);
        $share_users = $this->Post->PostShareUser->getShareUserListByPost($post_id);
        //権限チェック
        if (!in_array($this->Auth->user('id'), array_merge($share_users, [$room_info['Post']['user_id']]))) {
            //権限が無ければ空のデータをレスポンスする
            return $this->_ajaxGetResponse([]);
        }
        //トピック既読処理
        $this->Post->PostRead->red($post_id);
        $room_info = $this->Post->getPostById($post_id);

        $room_info['User']['photo_path'] = $this->Post->getPhotoPath($room_info['User']);
        //auto link
        $room_info['Post']['body'] = nl2br($text_ex->autoLink($room_info['Post']['body']));
        $room_info['AttachedFileHtml'] = $this->fileUploadMessagePageRender($room_info['PostFile'], $post_id);

        // 画面表示用に自分以外のメッセージ共有者１人の情報を取得する
        $first_share_user = [];
        if ($room_info['Post']['user_id'] != $this->Auth->user('id')) {
            $first_share_user['User'] = $room_info['User'];
        } else {
            if ($share_users) {
                $first_share_user = $this->User->findById(current($share_users));
            }
        }

        $res = [
            'auth_info'        => [
                'user_id'    => $this->Auth->user('id'),
                'language'   => $this->Auth->user('language'),
                'photo_path' => $this->Post->getPhotoPath($this->Auth->user()),
            ],
            'room_info'        => $room_info,
            'share_users'      => $share_users,
            'first_share_user' => $first_share_user,
            'comment_count'    => $this->Post->Comment->getCommentCount($post_id)
        ];

        //対象のメッセージルーム(Post)のnotifyがあれば削除する
        //メッセージなら該当するnotifyをredisから削除する
        //なお通知は1ルームあたりからなず1個のため、notify_id = post_id
        $this->NotifyBiz->removeMessageNotification($post_id);
        //未読通知件数を更新
        $this->NotifyBiz->updateCountNewMessageNotification();

        return $this->_ajaxGetResponse($res);
    }

    /**
     * メッセージ一覧を返す
     * ただし、１つのトピックの１件目のメッセージは含まれない
     *
     * @param     $post_id
     * @param     $limit
     * @param     $page_num
     * @param int $start メッセージ投稿時間：指定すると、この時間以降のメッセージのみを返す
     *
     * @return CakeResponse
     */
    public function ajax_get_message($post_id, $limit, $page_num, $start = null)
    {
        $this->_ajaxPreProcess();
        //メッセージを既読に
        $this->Post->Comment->CommentRead->redAllByPostId($post_id);

        $message_list = $this->Post->Comment->getPostsComment($post_id, $limit, $page_num, 'desc', ['start' => $start]);
        foreach ($message_list as $key => $item) {
            $message_list[$key]['AttachedFileHtml'] = $this->fileUploadMessagePageRender($item['CommentFile'],
                $post_id);
        }
        $convert_msg_data = $this->Post->Comment->convertData($message_list);

        $result = ['message_list' => $convert_msg_data];
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_put_message($post_id)
    {
        $this->_ajaxPreProcess('post');

        $params['Comment']['post_id'] = $post_id;
        $params['Comment']['body'] = $this->request->data('body');
        $params['file_id'] = $this->request->data('file_redis_key');
        if (!$comment_id = $this->Post->Comment->add($params)) {
            //失敗の場合
            return $this->_ajaxGetResponse([]);
        }

        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_MESSAGE, $post_id, $comment_id);
        $detail_comment = $this->Post->Comment->getComment($comment_id);
        $detail_comment['AttachedFileHtml'] = $this->fileUploadMessagePageRender($detail_comment['CommentFile'],
            $post_id);
        $convert_data = $this->Post->Comment->convertData($detail_comment);

        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $pusher->trigger('message-channel-' . $post_id, 'new_message', $convert_data,
            $this->request->data('socket_id'));
        $this->Mixpanel->trackMessage($post_id);
        return $this->_ajaxGetResponse($detail_comment);
    }

    function fileUploadMessagePageRender($data, $post_id)
    {
        $attached_files = '';
        foreach ($data as $attached_file) {
            if (in_array(strtolower($attached_file['AttachedFile']['file_ext']),
                    ['jpg', 'jpeg', 'gif', 'png']) === true
            ) {
                $this->set('message_page_image', true);
            } else {
                $this->set('message_page_image', false);
            }
            $this->set('post_id', $post_id);
            $this->set('data', $attached_file);
            $response = $this->render('Feed/attached_file_item');
            $attached_files .= $response->__toString();
        }
        return $attached_files;
    }

    public function ajax_put_message_read($post_id, $comment_id)
    {
        $this->_ajaxPreProcess();
        $res = $this->Post->Comment->CommentRead->red([$comment_id]);
        if ($res === true) {
            $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
            $pusher->trigger('message-channel-' . $post_id, 'read_message', $comment_id);
            //通知の削除が通知データ作成以前に行われてしまう為、ある程度待って削除処理実行
            sleep(5);
            $this->NotifyBiz->removeMessageNotification($post_id);
            $this->NotifyBiz->updateCountNewMessageNotification();
        }
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_get_action_list_more()
    {
        $param_named = $this->request->params['named'];
        $this->_ajaxPreProcess();
        if (isset($param_named['page']) && !empty($param_named['page'])) {
            $page_num = $param_named['page'];
        } else {
            $page_num = 1;
        }
        $start = null;
        $end = null;
        if (isset($this->request->params['named']['evaluate_term_id'])) {
            $term = $this->Team->EvaluateTerm->findById($this->request->params['named']['evaluate_term_id']);
            if (isset($term['EvaluateTerm'])) {
                $start = $term['EvaluateTerm']['start_date'];
                $end = $term['EvaluateTerm']['end_date'];
            }
        }

        $posts = $this->Post->get($page_num, POST_FEED_PAGE_ITEMS_NUMBER, $start, $end, $this->request->params);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $this->set('without_header', true);
        $this->set('without_add_comment', true);
        $response = $this->render('Feed/posts');
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
        if (Hash::get($param_named, 'page_type') == 'image') {
            $item_num = MY_PAGE_CUBE_ACTION_IMG_NUMBER;
            $elm_path = "cube_img_blocks";
        }
        // 投稿一覧取得
        $posts = $this->Post->get($page_num, $item_num, $start, $end, $this->request->params);
        $this->set('posts', $posts);
        $this->set('long_text', false);
        $without_header = Hash::get($param_named, 'without_header');
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
            Hash::get($param_named, 'file_type'));
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
        $start = null;
        $end = null;
        if (isset($this->request->params['named']['evaluate_term_id'])) {
            $term = $this->Team->EvaluateTerm->findById($this->request->params['named']['evaluate_term_id']);
            if (isset($term['EvaluateTerm'])) {
                $start = $term['EvaluateTerm']['start_date'];
                $end = $term['EvaluateTerm']['end_date'];
            }
        }
        $posts = $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, $start, $end, $this->request->params);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_action_list');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_old_comment($get_num)
    {
        $post_id = Hash::get($this->request->params, 'named.post_id');
        $this->_ajaxPreProcess();
        $comments = $this->Post->Comment->getPostsComment($post_id, $get_num);
        $long_text = false;
        if (isset($this->request->params['named']['long_text'])) {
            $long_text = $this->request->params['named']['long_text'];
        }
        $this->set('long_text', $long_text);
        $this->set(compact('comments'));

        // コメントを既読にする
        $this->Post->Comment->CommentRead->red(Hash::extract($comments, '{n}.Comment.id'));

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
        $post_id = Hash::get($this->request->params, 'named.post_id');
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
        $post_id = Hash::get($this->request->params, 'named.post_id');
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
        } else {
            $result['error'] = true;
            $result['msg'] = __("An error has occurred.");
        }
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_edit_comment_form($id_prefix = null)
    {
        $comment_id = Hash::get($this->request->params, 'named.comment_id');
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
        } else {
            $result['error'] = true;
            $result['msg'] = __("An error has occurred.");
        }
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_edit_post_form()
    {
        $post_id = Hash::get($this->request->params, 'named.post_id');
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
        } else {
            $result['error'] = true;
            $result['msg'] = __("An error has occurred.");
        }
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_post_like()
    {
        $post_id = Hash::get($this->request->params, 'named.post_id');
        $this->_ajaxPreProcess();
        $res = $this->Post->PostLike->changeLike($post_id);
        if ($res['is_liked']) {
            $post = $this->Post->findById($post_id);
            $type = Hash::get($post, 'Post.type');
            $this->Mixpanel->trackLike($type);
        }
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_comment_like()
    {
        $comment_id = Hash::get($this->request->params, 'named.comment_id');
        $this->_ajaxPreProcess();
        $res = $this->Post->Comment->CommentLike->changeLike($comment_id);
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_get_post_liked_users()
    {
        $post_id = Hash::get($this->request->params, 'named.post_id');
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
        $post_id = Hash::get($this->request->params, 'named.post_id');
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
        $comment_id = Hash::get($this->request->params, 'named.comment_id');
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
        $comment_id = Hash::get($this->request->params, 'named.comment_id');
        $this->_ajaxPreProcess();
        $red_users = $this->Post->Comment->CommentRead->getRedUsers($comment_id);
        $this->set(compact('red_users'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/modal_comment_red_users');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_message_red_users()
    {
        $comment_id = Hash::get($this->request->params, 'named.comment_id');
        $post_id = Hash::get($this->request->params, 'named.post_id');
        $this->_ajaxPreProcess();
        $red_users = [];
        $model = null;
        if ($comment_id) {
            $red_users = $this->Post->Comment->CommentRead->getRedUsers($comment_id);
            $model = 'CommentRead';
        } elseif ($post_id) {
            $red_users = $this->Post->PostRead->getRedUsers($post_id);
            $model = 'PostRead';
        }
        $this->set(compact('red_users', 'model'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Feed/modal_message_red_users');
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
        $this->Post->id = Hash::get($this->request->data, 'Comment.post_id');
        $post = $this->Post->findById($this->Post->id);
        $type = Hash::get($post, 'Post.type');
        try {
            if (!$this->Post->exists()) {
                throw new RuntimeException(__("This post was deleted."));
            }

            // OGP 情報を取得する URL が含まれるテキスト
            // フロントの JS でプレビューが正しく取得出来た場合は、site_info_url に URL が含まれている
            // それ以外の場合は body テキスト全体から URL を検出する
            $url_text = $this->request->data('Comment.site_info_url');
            if (!$url_text) {
                $url_text = $this->request->data('Comment.body');
            }

            // ogbをインサートデータに追加
            $this->request->data['Comment'] = $this->_addOgpIndexes(Hash::get($this->request->data, 'Comment'),
                $url_text);

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
                }
                //mixpanel
                $this->Mixpanel->trackComment($type, $this->Post->Comment->getLastInsertID());

                $result['msg'] = __("Commented.");
            } else {
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
            $this->set([
                'posts' => $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null,
                    $this->request->params)
            ]);
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

    public function ajax_circle_feed()
    {
        $this->User->CircleMember->updateUnreadCount($this->request->params['circle_id']);
        list($user_status, $circle_member_count) = $this->_setCircleCommonVariables();
        $this->ajax_get_feed("Feed/posts", $user_status, $circle_member_count);
    }

    public function attached_file_list()
    {
        $this->_setCircleCommonVariables();
        $this->_setViewValOnRightColumn();
        $circle_id = Hash::get($this->request->params, "named.circle_id");
        if (!$circle_id) {
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }

        $file_type_options = $this->Post->PostFile->AttachedFile->getFileTypeOptions();
        $files = $this->Post->getFilesOnCircle($circle_id, 1, FILE_LIST_PAGE_NUMBER, null, null,
            Hash::get($this->request->params, 'named.file_type'));

        $circle_file_list_base_url = Router::url(
            [
                'controller' => 'posts',
                'action'     => 'attached_file_list',
                'circle_id'  => $circle_id
            ]);

        $this->set(compact('files', 'file_type_options', 'circle_file_list_base_url'));
        return $this->render();
    }

    /**
     * 添付ファイルのダウンロード
     *
     * @return CakeResponse
     */
    public function attached_file_download()
    {
        // データが存在するか確認
        $file = $this->Post->PostFile->AttachedFile->findById($this->request->params['named']['file_id']);
        if (!$file) {
            throw new NotFoundException(__("This file doesn't exist."));
        }

        // ファイルへのアクセス権限があるか確認
        if (!$this->Post->PostFile->AttachedFile->isReadable($this->request->params['named']['file_id'])) {
            throw new NotFoundException(__("This file doesn't exist."));
        }

        // ファイルデータを取得
        $url = $this->Post->PostFile->AttachedFile->getFileUrl($this->request->params['named']['file_id']);
        $res = $this->_getHttpSocket()->get(Router::url($url, true));
        if (!$res->body) {
            throw new NotFoundException(__("This file doesn't exist."));
        }

        // safari は日本語ファイル名が文字化けするので特別扱い
        $browser = $this->getBrowser();
        if ($browser['browser'] == 'Safari') {
            $this->response->header('Content-Disposition',
                sprintf('attachment; filename="%s";', $file['AttachedFile']['attached_file_name']));
        } else {
            $this->response->header('Content-Disposition',
                sprintf('attachment; filename="%s"; filename*=UTF-8\'\'%s',
                    $file['AttachedFile']['attached_file_name'],
                    rawurlencode($file['AttachedFile']['attached_file_name'])));
        }
        $this->response->type('application/octet-stream');
        $this->response->length(strlen($res->body));
        $this->response->body($res->body);
        return $this->response;
    }

    /**
     * HttpSocket クラスを返す
     * （主にコントローラのユニットテスト用）
     */
    function _getHttpSocket()
    {
        App::uses('HttpSocket', 'Network/Http');
        return new HttpSocket();
    }

    function _setCircleCommonVariables()
    {
        $params = $this->request->params;
        $params = array_merge($params, $params['named']);
        $this->_setCurrentCircle();
        $this->_setFeedMoreReadUrl();

        if (isset($params['post_id'])) {
            $this->set('long_text', true);
        } else {
            $this->set('long_text', false);
        }

        $feed_filter = null;
        $user_status = null;
        $circle_member_count = 0;
        if ($circle_id = Hash::get($params, 'circle_id')) {
            $user_status = $this->_userCircleStatus($circle_id);
            $circle_status = $this->Post->Circle->CircleMember->getShowHideStatus($this->Auth->user('id'),
                $circle_id);
            //サークル指定の場合はメンバーリスト取得
            $circle_member_count = $this->User->CircleMember->getActiveMemberCount($params['circle_id']);
            $this->set(compact('user_status', 'circle_status', 'circle_member_count'));
        }
        //抽出条件
        if ($circle_id) {
            $feed_filter = 'circle';
        } elseif (isset($params['filter_goal'])) {
            $feed_filter = 'goal';
        }

        $this->set('common_form_type', 'post');
        $this->set(compact('feed_filter', 'circle_id', 'params'));

        return array($user_status, $circle_member_count);
    }

    public function ajax_get_share_circles_users_modal()
    {
        $post_id = Hash::get($this->request->params, 'named.post_id');
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
     * １メッセージに参加しているメンバー一覧を表示
     *
     * @return CakeResponse
     */
    public function ajax_get_share_message_modal()
    {
        $post_id = Hash::get($this->request->params, 'named.post_id');
        $this->_ajaxPreProcess();
        /** @noinspection PhpUndefinedMethodInspection */
        $users = $this->Post->PostShareUser->getShareUsersByPost($post_id);
        $post = $this->Post->getPostById($post_id);
        array_unshift($users, ['User' => $post['User']]);
        $total_share_user_count = $this->_getTotalShareUserCount([], $users);
        $this->set(compact('users', 'total_share_user_count'));
        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_message_range');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    /**
     * ファイルアップロード
     * JSON レスポンス形式
     * {
     *   error: bool,   // エラーが発生した場合に true
     *   msg: string,   // 処理結果を示すメッセージ
     *   id: string,    // ファイルID
     * }
     *
     * @return CakeResponse
     */
    public function ajax_upload_file()
    {
        $this->_ajaxPreProcess();
        $file_id = $this->Post->PostFile->AttachedFile->preUploadFile($this->request->params['form']);
        return $this->_ajaxGetResponse([
            'error' => $file_id ? false : true,
            'msg'   => $file_id ? "" : __('Failed to upload.'),
            'id'    => $file_id ? $file_id : "",
        ]);
    }

    /**
     * アップロードしたファイルを削除
     * JSON レスポンス形式
     * {
     *   error: bool,   // エラーが発生した場合に true
     *   msg: string,   // 処理結果を示すメッセージ
     *   id: string,    // ファイルID
     * }
     *
     * @return CakeResponse
     */
    public function ajax_remove_file()
    {
        $this->_ajaxPreProcess();
        $success = $this->Post->PostFile->AttachedFile->cancelUploadFile($this->request->data('AttachedFile.file_id'));
        return $this->_ajaxGetResponse([
            'error' => !$success,
            'msg'   => $success
                ? __('Deleted the file.')
                : __('Failed to delete the file.'),
            'id'    => "",
        ]);
    }

    /**
     * OGP のデータを取得する
     *
     * @return CakeResponse
     */
    public function ajax_get_ogp_info()
    {
        $this->_ajaxPreProcess();
        $res = [];
        $ogp = $this->Ogp->getOgpByUrlInText($this->request->query('text'));
        if (isset($ogp['title'])) {
            $res = $ogp;
            $this->set('site_info', $ogp);
            $response = $this->render('/Elements/Feed/site_info_block');
            $res['html'] = $response->__toString();
        }
        return $this->_ajaxGetResponse($res);
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
        if (!isset($ogp['title'])) {
            $requestData['site_info'] = null;
            $requestData['site_photo'] = null;
            return $requestData;
        }

        // ogpが取得できた場合
        $requestData['site_info'] = json_encode($ogp);
        if (isset($ogp['image'])) {
            $ext = UploadBehavior::getImgExtensionFromUrl($ogp['image']);
            if (!$ext) {
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
        $socketId = Hash::get($this->request->data, 'socket_id');
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
            $this->Pnotify->outError(__("Error in the URL."));
            return $this->redirect($this->referer());
        }

        if ($this->Post->Circle->CircleMember->joinNewMember($this->request->params['named']['circle_id'])) {
            $this->Pnotify->outSuccess(__("Joined the circle"));
        } else {
            $this->Pnotify->outError(__("Failed to join the circle."));
        }
        return $this->redirect($this->request->referer());

    }

    public function unjoin_circle()
    {
        if (!$this->_isAvailCircle()) {
            $this->Pnotify->outError(__("Error in the URL."));
            return $this->redirect($this->referer());
        }
        $circle_id = $this->request->params['named']['circle_id'];
        if ($circle_id == $this->Post->Circle->getTeamAllCircleId()) {
            $this->Pnotify->outError(__("Not allowed to leave this circle."));
            return $this->redirect($this->referer());
        }
        $this->Post->Circle->CircleMember->unjoinMember($circle_id);
        $this->Pnotify->outSuccess(__("Left the circle"));
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
        } else {
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
            $this->Post->Circle->CircleMember->circleStatusToggle($circle_id, $status);
            return $this->redirect($this->request->referer());
        } else {
            throw new NotFoundException(__("Invalid Request"));
        }
    }

    public function ajax_add_post_for_setup_guide()
    {
        $this->_ajaxPreProcess();
        $res = $this->_addPost();
        // 非同期処理のためcakeによるフラッシュメッセージは不要。
        $this->Session->delete('Message');
        $msg = __("Posted.");
        $error = false;
        if (!$res) {
            $msg = __("Failed to post.");
            $error = true;
        } else {
            $this->Pnotify->outSuccess(__("Posted."));
        }

        //セットアップガイドステータスの更新
        $this->updateSetupStatusIfNotCompleted();

        return $this->_ajaxGetResponse([
            'error'             => $error,
            'msg'               => $msg,
            'validation_errors' => array_values($this->Post->validationErrors)
        ]);
    }

}
