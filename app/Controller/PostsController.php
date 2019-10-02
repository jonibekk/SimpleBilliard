<?php
App::uses('AppController', 'Controller');
App::import('Service', 'AttachedFileService');
App::import('Service', 'PostService');
App::import('Service', 'PostDraftService');
App::import('Service', 'TranslationService');
App::uses('TeamStatus', 'Lib/Status');
App::uses('Translation', 'Model');
App::uses('Video', 'Model');
App::uses('VideoStream', 'Model');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');

use Goalous\Enum as Enum;
use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;

/**
 * Posts Controller
 *
 * @property Post $Post
 */
class PostsController extends AppController
{
    use TranslationNotificationTrait;

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

    /**
     * @return \Cake\Network\Response|CakeResponse|null
     * @deprecated
     */
    public function message()
    {
        $this->_logOldRequest(__CLASS__, __METHOD__);
        $this->Notification->outInfo(__('Due to using an old URL, you have been redirected to this page.'));
        return $this->redirect('/topics');
        //TODO should be removed.

        $this->layout = LAYOUT_ONE_COLUMN;
        $this->set('without_footer', true);
        return $this->render();
    }

    /**
     * @return \Cake\Network\Response|CakeResponse|null
     * @deprecated
     */
    public function message_list()
    {
        // モバイルアプリ以外の場合はこのurlへの動線が存在してはいけないので、ログを残す。
        // モバイルアプリはリリースのタイミングまで動線が残る。
        if (!$this->is_mb_app) {
            $this->_logOldRequest(__CLASS__, __METHOD__);
        }
        return $this->redirect("/topics/");

        //TODO should be removed.
        // 宛先のユーザー情報取得
        $targetUserId = $this->request->query('userId');
        $targetUserId = is_numeric($targetUserId) ? $targetUserId : '';
        $this->set(compact('targetUserId'));

        return $this->render();
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

    public function add()
    {
        $this->_addPost();
        $this->redirect($this->_getRedirectUrl());
    }

    /**
     * add method
     *
     * @return void
     * @throws RuntimeException
     */
    public function _addPost()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');
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

        $userId = $this->Auth->user('id');
        $teamId = TeamStatus::getCurrentTeam()->getTeamId();

        $countVideoStreamIds =
            isset($this->request->data['video_stream_id']) && is_array($this->request->data['video_stream_id'])
                ? count($this->request->data['video_stream_id']) : 0;
        if (1 < $countVideoStreamIds) {
            $this->Notification->outError(__('You can only post one video file.'));
            return false;
        }
        if (1 === $countVideoStreamIds) {
            /** @var VideoStream $VideoStream */
            $VideoStream = ClassRegistry::init("VideoStream");

            $videoStreamId = reset($this->request->data['video_stream_id']);
            $videoStream = $VideoStream->findById($videoStreamId);
            $videoStream = reset($videoStream);

            $user = $this->User->getById($this->Auth->user('id'));
            $teamId = $this->current_team_id;
            $transcodeStatus = new Enum\Model\Video\VideoTranscodeStatus(intval($videoStream['transcode_status']));
            $logDataArray = [
                'video_streams.id' => $videoStream['id'],
                'transcode_status' => sprintf('%s:%s', $transcodeStatus->getValue(), $transcodeStatus->getKey()),
            ];
            switch ($transcodeStatus->getValue()) {
                case Enum\Model\Video\VideoTranscodeStatus::UPLOADING:
                case Enum\Model\Video\VideoTranscodeStatus::UPLOAD_COMPLETE:
                case Enum\Model\Video\VideoTranscodeStatus::QUEUED:
                case Enum\Model\Video\VideoTranscodeStatus::TRANSCODING:
                case Enum\Model\Video\VideoTranscodeStatus::ERROR:
                    // create draft post
                    GoalousLog::info("video post creating draft post", $logDataArray);
                    /** @var PostDraftService $PostDraftService */
                    $PostDraftService = ClassRegistry::init("PostDraftService");
                    $postDraft = $PostDraftService->createPostDraftWithResources($this->request->data,
                        $userId,
                        $teamId,
                        [
                            [
                                'is_video' => true,
                                'video_stream_id' => $videoStream['id'],
                            ]
                        ]
                    );
                    if (false === $postDraft) {
                        // バリデーションエラーのケース
                        if (!empty($this->Post->validationErrors)) {
                            $error_msg = array_shift($this->Post->validationErrors);
                            $this->Notification->outError($error_msg[0], ['title' => __("Failed to post.")]);
                        } else {
                            $this->Notification->outError(__("Failed to post."));
                        }
                        return false;
                    }
                    return true;
                case Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE:
                    GoalousLog::info("video post creating draft post", $logDataArray);
                    $successSavedPost = $PostService->addNormalWithTransaction($this->request->data, $userId, $teamId,
                        [$videoStream]);
                    // 保存に失敗
                    if (false === $successSavedPost) {
                        // バリデーションエラーのケース
                        if (!empty($this->Post->validationErrors)) {
                            $error_msg = array_shift($this->Post->validationErrors);
                            $this->Notification->outError($error_msg[0], ['title' => __("Failed to post.")]);
                        } else {
                            $this->Notification->outError(__("Failed to post."));
                        }
                        return false;
                    }
                    $this->processAfterPosted($successSavedPost['id']);
                    return true;
                default:
                    GoalousLog::info("video post error", $logDataArray);
                    throw new RuntimeException(sprintf("invalid status code: %s", $transcodeStatus->getValue()));
                    break;
            }
        }

        // 投稿を保存
        $successSavedPost = $PostService->addNormalWithTransaction($this->request->data, $userId, $teamId);

        // 保存に失敗
        if (false === $successSavedPost) {
            // バリデーションエラーのケース
            if (!empty($this->Post->validationErrors)) {
                $error_msg = array_shift($this->Post->validationErrors);
                $this->Notification->outError($error_msg[0], ['title' => __("Failed to post.")]);
            } else {
                $this->Notification->outError(__("Failed to post."));
            }
            return false;
        }

        $this->processAfterPosted($successSavedPost['id']);

        return true;
    }

    private function processAfterPosted(int $postedPostId)
    {
        $this->_updateSetupStatusIfNotCompleted();

        $notify_type = NotifySetting::TYPE_FEED_POST;
        if (Hash::get($this->request->data, 'Post.type') == Post::TYPE_MESSAGE) {
            $notify_type = NotifySetting::TYPE_MESSAGE;
        }
        $this->NotifyBiz->execSendNotify($notify_type, $postedPostId);

        $socketId = Hash::get($this->request->data, 'socket_id');
        $share = explode(",", Hash::get($this->request->data, 'Post.share'));

        //何らかの原因でsocketIdが無いもしくは、共有先指定なしの場合は以降の処理(通知、イベントトラッキング)を行わない
        if (!$socketId || $share[0] === "") {
            $this->Notification->outSuccess(__("Posted."));
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

        $this->Notification->outSuccess(__("Posted."));

        // Make translation
        $teamId = TeamStatus::getCurrentTeam()->getTeamId();

        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        if ($TranslationService->canTranslate($teamId)) {
            try {
                $TranslationService->createDefaultTranslation($teamId, TranslationContentType::CIRCLE_POST(), $postedPostId);
                // I need to write Email send process here, NotifyBizComponent Can't call from Service class.
                $this->sendTranslationUsageNotification($teamId);
            } catch (Exception $e) {
                GoalousLog::error('Failed create translation on new post', [
                    'message'  => $e->getMessage(),
                    'posts.id' => $this->Post->getLastInsertID(),
                ]);
            }
        }

        return true;
    }

    /**
     * post_delete method
     *
     * @return void
     * @throws NotFoundException
     */
    public function post_delete()
    {
        $this->Post->id = Hash::get($this->request->params, 'named.post_id');
        if (!$this->Post->exists()) {
            throw new NotFoundException(__("This post no longer exist."));
        }
        if (!$this->Post->isOwner($this->Auth->user('id')) && !$this->User->TeamMember->myStatusWithTeam['TeamMember']['admin_flg']) {
            throw new NotFoundException(__("This isn't your post."));
        }
        $this->request->allowMethod('post', 'delete');
        $this->Post->delete();

        $this->Post->PostFile->AttachedFile->deleteAllRelatedFiles($this->Post->id,
            AttachedFile::TYPE_MODEL_POST);

        // Delete translations
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');
        $Translation->eraseAllTranslations(TranslationContentType::CIRCLE_POST(), $this->Post->id);

        $this->Notification->outSuccess(__("Deleted the post."));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * post_edit method
     *
     * @return void
     * @throws NotFoundException
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
                $this->Notification->outSuccess(__("Saved changes."));
            } else {
                $error_msg = array_shift($this->Post->validationErrors);
                $this->Notification->outError($error_msg[0], ['title' => __("Failed to save changes.")]);
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
     * @return void
     * @throws NotFoundException
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

        $this->Notification->outSuccess(__("Deleted the comment."));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * comment_edit method
     *
     * @param $comment_id
     *
     * @return void
     * @throws NotFoundException
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
            $this->Notification->outSuccess(__("Edited the comment."));
        } else {
            $error_msg = array_shift($this->Post->Comment->validationErrors);
            $this->Notification->outError($error_msg[0], ['title' => __("Failed to save changes to the comment.")]);
        }
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * Getting feed data as Html
     * It's used for infinity scroll. Not initializing.
     *
     * @return CakeResponse|null
     */
    public function ajax_get_feed()
    {
        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $paramNamed = $this->request->params['named'];
        $this->_ajaxPreProcess();

        $notify_id = $this->request->query('notify_id');
        if ($notify_id) {
            $this->set('long_text', true);
        } else {
            $this->set('long_text', false);
        }

        $pageNum = $paramNamed['page'] ?? 1;

        //一ヶ月以前を指定された場合
        $monthIndex = Hash::get($paramNamed, 'month_index');
        if ($monthIndex) {
            $postRange = $PostService->getRangeByMonthIndex($monthIndex);
        } else {
            $postRange = ['start' => null, 'end' => null];
        }
        $posts = $this->Post->get($pageNum, POST_FEED_PAGE_ITEMS_NUMBER, $postRange['start'], $postRange['end'],
            $this->request->params);

        $this->set(compact('posts'));
        $this->set('enable_translation', true);

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        //1.フィードのスクロールによる投稿取得 2.notifyから投稿詳細ページに遷移した場合の投稿取得
        //1,2どちらのケースでもこのコードが実行されるが、「not exist」メッセージを出すのは2のケースのみのため、
        //ここで分岐をする必要がある。
        $isNotifyPostPermanentPage = isset($this->request->params['post_id']) && $notify_id;
        if ($isNotifyPostPermanentPage && !$posts) {
            $response = $this->render('Feed/post_not_found');
        } else {
            $response = $this->render("Feed/posts");
        }

        $html = $response->__toString();
        $result = array(
            'html'          => $html,
            'count'         => count($posts),
            'page_item_num' => POST_FEED_PAGE_ITEMS_NUMBER,
            'start'         => $postRange['start'] ?? REQUEST_TIMESTAMP - MONTH,
        );
        if (isset($posts[0]['Post']['modified'])) {
            $result['post_time_before'] = $posts[0]['Post']['modified'];
        }

        return $this->_ajaxGetResponse($result);
    }

    /**
     * 月のインデックスからフィードの取得期間を取得
     *
     * @param int $monthIndex
     *
     * @return array ['start'=>unixtimestamp,'end'=>unixtimestamp]
     */
    function _getRangeByMonthIndex(int $monthIndex): array
    {
        $start_month_offset = $monthIndex + 1;
        $ret['end'] = strtotime("-{$monthIndex} months", REQUEST_TIMESTAMP);
        $ret['start'] = strtotime("-{$start_month_offset} months", REQUEST_TIMESTAMP);
        return $ret;
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
            $term = $this->Team->Term->findById($this->request->params['named']['evaluate_term_id']);
            if (isset($term['Term'])) {
                $start = $term['Term']['start_date'];
                $end = $term['Term']['end_date'];
            }
        }

        $posts = $this->Post->get($page_num, POST_FEED_PAGE_ITEMS_NUMBER, $start, $end, $this->request->params);
        $this->set(compact('posts'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $this->set('without_header', true);
        $this->set('without_add_comment', true);
        $this->set('long_text', false);
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
        $namedParams = $this->request->params['named'];
        $this->_ajaxPreProcess();

        // 表示するページ
        $page_num = 1;
        if (isset($namedParams['page']) && !empty($namedParams['page'])) {
            $page_num = $namedParams['page'];
        }
        // max end timestamp
        if (Hash::get($namedParams, 'max_end_timestamp')) {
            $maxEndTimestamp = $namedParams['max_end_timestamp'];
        } else {
            $maxEndTimestamp = REQUEST_TIMESTAMP;
        }

        // min start timestamp
        if (Hash::get($namedParams, 'min_start_timestamp')) {
            $minStartTimestamp = $namedParams['min_start_timestamp'];
        } else {
            // if min_start_timestamp param not exists, team created timestamp will be $minStartTimestamp
            $team = $this->Team->getCurrentTeam();
            $minStartTimestamp = $team['Team']['created'];
        }

        // データ取得期間
        $startTimestamp = strtotime("-1 month", $maxEndTimestamp);
        $endTimestamp = $maxEndTimestamp;

        if (Hash::get($namedParams, 'month_index')) {
            // 一ヶ月以前を指定された場合
            $end_month_offset = $namedParams['month_index'];
            $start_month_offset = $end_month_offset + 1;
            $endTimestamp = strtotime("-{$end_month_offset} months", $maxEndTimestamp);
            $startTimestamp = strtotime("-{$start_month_offset} months", $maxEndTimestamp);
        }

        //取得件数
        $item_num = POST_FEED_PAGE_ITEMS_NUMBER;
        //エレメントpath
        $elm_path = "Feed/posts";
        if (Hash::get($namedParams, 'page_type') == 'image') {
            $item_num = MY_PAGE_CUBE_ACTION_IMG_NUMBER;
            $elm_path = "cube_img_blocks";
        }

        $posts = [];
        // $endTimestamp が $minStartTimestamp　より古くなる場合は $posts取りに行かない
        if ($endTimestamp > $minStartTimestamp) {
            // $startTimestamp が $minStartTimestamp　より古くなる場合は $startTimestamp に　$minStartTimestamp　をセット
            if ($startTimestamp < $minStartTimestamp) {
                $startTimestamp = $minStartTimestamp;
            }
            // 投稿一覧取得
            $posts = $this->Post->get($page_num, $item_num, $startTimestamp, $endTimestamp, $this->request->params);
        }
        $this->set('posts', $posts);
        $this->set('long_text', false);
        $without_header = Hash::get($namedParams, 'without_header');
        $this->set(compact('without_header'));

        // エレメントの出力を変数に格納する
        // htmlレンダリング結果
        $response = $this->render($elm_path);
        $html = $response->__toString();
        $result = array(
            'html'          => $html,
            'count'         => count($posts),
            'page_item_num' => $item_num,
            'start'         => $startTimestamp ? $startTimestamp : REQUEST_TIMESTAMP - MONTH,
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
            $term = $this->Team->Term->findById($this->request->params['named']['evaluate_term_id']);
            if (isset($term['Term'])) {
                $start = GoalousDateTime::createFromFormat('Y-m-d', $term['Term']['start_date'])->startOfDay()->format('Y-m-d H:i:s');
                $end = GoalousDateTime::createFromFormat('Y-m-d', $term['Term']['end_date'])->endOfDay()->format('Y-m-d H:i:s');
            }
        }
        $posts = $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, $start, $end, $this->request->params);
        $extractedByKR = isset($this->request->params['named']['key_result_id']);

        $this->set('long_text', false);
        $this->set(compact('posts', 'extractedByKR'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_action_list');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_old_comment($get_num)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        $post_id = Hash::get($this->request->params, 'named.post_id');
        $this->_ajaxPreProcess();
        $comments = $this->Post->Comment->getPostsComment($post_id, $get_num);
        $long_text = false;
        if (isset($this->request->params['named']['long_text'])) {
            $long_text = $this->request->params['named']['long_text'];
        }

        $post = $Post->getById($post_id);
        $this->set('long_text', $long_text);
        $this->set(compact('comments'));
        $this->set('enable_translation', true);
        $this->set('post_type', $post['type']);

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
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        $post_id = Hash::get($this->request->params, 'named.post_id');
        $this->_ajaxPreProcess();
        $comments = $this->Post->Comment->getLatestPostsComment($post_id, $last_comment_id);

        $postId = Hash::get($comments[0], 'Comment.post_id');
        $post = $Post->getById($postId);

        $this->set(compact('comments'));
        $this->set('enable_translation', true);
        $this->set('post_type', $post['type']);

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
        // If specified circle_id
        if (!empty($this->request->params['circle_id'])) {
            $circleId = $this->request->params['circle_id'];
            $urlCircleFeed = sprintf('/circles/%s/posts', $circleId);
            if (ENV_NAME == 'local') {
                $urlCircleFeed = "http://local.goalous.com:5790".$urlCircleFeed;
            }
            $this->redirect($urlCircleFeed);
            return;
        }

        // If specified post_id, showing post detail.
        if (!empty($this->request->params['post_id'])) {
            $postId = $this->request->params['post_id'];
            /** @var Post $Post */
            $Post = ClassRegistry::init('Post');
            $post = $Post->findById($postId);
            $url = sprintf('/posts/%s?%s', $postId, http_build_query($this->request->query));
            if (ENV_NAME == 'local') {
                $url = "http://local.goalous.com:5790".$url;
            }
            do {
                if (empty($post)) {
                    // Post doesn't exists
                    // But redirecting to show new 404
                    $this->redirect($url);
                    break;
                }

                $postType = (int)$post['Post']['type'];
                $typesCanViewOnAngular = [
                    Enum\Model\Post\Type::NORMAL
                ];
                if (!in_array($postType, $typesCanViewOnAngular)) {
                    // Angular could not show this type of post yet.
                    // Show post on old Goalous.
                    break;
                }

                /** @var PostService $PostService */
                $PostService = ClassRegistry::init('PostService');
                if (!$PostService->checkUserAccessToCirclePost($this->Auth->user('id'), $postId)) {
                    // User can't access post
                    // But redirecting to show new 404
                    $this->redirect($url);
                    break;
                }

                // User can see this type of post on Angular
                $this->redirect($url);
                return;
            } while (false);
        }

        $this->_setCircleCommonVariables();

        try {
            $this->set([
                'posts' => $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null,
                    $this->request->params)
            ]);
            $this->set('enable_translation', true);

            // setting draft post data if having circle_id
            /** @var PostDraftService $PostDraftService */
            $PostDraftService = ClassRegistry::init('PostDraftService');
            $circleId = Hash::get($this->request->params, 'circle_id');
            if (isset($circleId) && AppUtil::isInt($circleId)) {
                $this->User->CircleMember->updateUnreadCount($circleId);
                $this->set('post_drafts', $PostDraftService->getPostDraftForFeed(
                    $this->Auth->user('id'),
                    TeamStatus::getCurrentTeam()->getTeamId(),
                    [$circleId]
                )
                );
            }
        } catch (RuntimeException $e) {
            //リファラとリクエストのURLが同じ場合は、メッセージを表示せず、ホームにリダイレクトする
            //サークルページに居て当該サークルから抜けた場合の対応
            $params = $this->request->params;
            unset($params['_Token']);
            if ($this->referer(null, true) == Router::url($params)) {
                $this->redirect('/');
            }
            $this->Notification->outError($e->getMessage());
            $this->redirect($this->referer());
        }
    }

    public function ajax_circle_feed()
    {
        $circleId = $this->request->params['circle_id'];
        $this->User->CircleMember->updateUnreadCount($circleId);
        list($userStatus, $circleMemberCount) = $this->_setCircleCommonVariables();

        $this->_ajaxPreProcess();

        $this->set('long_text', false);

        $posts = $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null,
            $this->request->params);
        $this->set(compact('posts'));
        $this->set('enable_translation', true);

        // setting draft post data if having circle_id
        /** @var PostDraftService $PostDraftService */
        $PostDraftService = ClassRegistry::init('PostDraftService');
        $circleId = Hash::get($this->request->params, 'circle_id');
        if (isset($circleId) && AppUtil::isInt($circleId)) {
            $this->set('post_drafts', $PostDraftService->getPostDraftForFeed(
                $this->Auth->user('id'),
                TeamStatus::getCurrentTeam()->getTeamId(),
                [$circleId]
            )
            );
        }

        $response = $this->render("Feed/posts");
        $html = $response->__toString();

        //getting circle image url
        $circle = $this->Post->Circle->findById($circleId);
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $circleImgUrl = $Upload->uploadUrl($circle, 'Circle.photo', ['style' => 'small']);

        $result = array(
            'html'                => $html,
            'count'               => count($posts),
            'page_item_num'       => POST_FEED_PAGE_ITEMS_NUMBER,
            'start'               => REQUEST_TIMESTAMP - MONTH,
            'circle_member_count' => $circleMemberCount,
            'user_status'         => $userStatus,
            'circle_img_url'      => $circleImgUrl,
        );
        if (isset($posts[0]['Post']['modified'])) {
            $result['post_time_before'] = $posts[0]['Post']['modified'];
        }

        return $this->_ajaxGetResponse($result);
    }

    public function attached_file_list()
    {
        $this->_setCircleCommonVariables();
        $circle_id = Hash::get($this->request->params, "named.circle_id");
        if (!$circle_id) {
            $this->Notification->outError(__("Invalid screen transition."));
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

        // Set header to prevent file name garbled
        $this->response->header('Content-Disposition',
            sprintf('attachment; filename="%s"; filename*=UTF-8\'\'%s',
                $file['AttachedFile']['attached_file_name'],
                rawurlencode($file['AttachedFile']['attached_file_name'])));

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
     * TODO:ファイルアップロード用APIをapi/v1に作成した為、リリース後削除
     *
     * @return CakeResponse
     * @deprecated
     *   ファイルアップロード
     *   JSON レスポンス形式
     *   {
     *   error: bool,   // エラーが発生した場合に true
     *   msg: string,   // 処理結果を示すメッセージ
     *   id: string,    // ファイルID
     *   }
     */
    public function ajax_upload_file(): CakeResponse
    {
        $this->_ajaxPreProcess();

        $form = Hash::get($this->request->params, 'form');
        if (empty($form)) {
            $this->response->statusCode(400);
            $ret['msg'] = __('Failed to upload.');
            return $this->_ajaxGetResponse($ret);
        }

        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init('AttachedFileService');
        $ret = $AttachedFileService->preUploadFile($form);
        if ($ret['error']) {
            $this->response->statusCode(400);
        }
        return $this->_ajaxGetResponse($ret);
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
     * @return CakeResponse
     * @deprecated
     * OGP のデータを取得する
     *
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

    /**
     * Join circle
     *
     * @return void
     */
    public function join_circle()
    {
        if (!$this->_isAvailCircle()) {
            $this->Notification->outError(__("Error in the URL."));
            return $this->redirect($this->referer());
        }

        App::import('Service', 'CircleService');
        /** @var ExperimentService $ExperimentService */
        $CircleService = ClassRegistry::init('CircleService');

        $circleId = Hash::get($this->request->params, 'named.circle_id');

        // Join circle
        $joinedSuccess = $CircleService->join($circleId, $this->Auth->user('id'));
        if ($joinedSuccess) {
            $this->Notification->outSuccess(__("Joined the circle"));
        } else {
            $this->Notification->outError(__("Failed to join the circle."));
            return $this->redirect($this->request->referer());
        }

        $this->_updateSetupStatusIfNotCompleted();

        // Notify to circle member
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_USER_JOIN, $circleId);

        return $this->redirect($this->request->referer());

    }

    public function unjoin_circle()
    {
        if (!$this->_isAvailCircle()) {
            $this->Notification->outError(__("Error in the URL."));
            return $this->redirect($this->referer());
        }

        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init("CircleService");

        $circleId = $this->request->params['named']['circle_id'];
        if ($circleId == $this->Post->Circle->getTeamAllCircleId()) {
            $this->Notification->outError(__("Not allowed to leave this circle."));
            return $this->redirect($this->referer());
        }
        $userId = $this->Auth->user('id');
        $CircleService->removeCircleMember($this->current_team_id, $circleId, $userId);
        $this->Notification->outSuccess(__("Left the circle"));
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
        if ($this->Post->Circle->CircleMember->isAdmin($this->Auth->user('id'), intval($circle_id))) {
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
            $this->Notification->outSuccess(__("Posted."));
        }

        //セットアップガイドステータスの更新
        $this->_updateSetupStatusIfNotCompleted();

        return $this->_ajaxGetResponse([
            'error'             => $error,
            'msg'               => $msg,
            'validation_errors' => array_values($this->Post->validationErrors)
        ]);
    }

}
