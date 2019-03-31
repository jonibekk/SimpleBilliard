<?php
App::uses('AppController', 'Controller');
App::import('Service', 'CircleService');
App::import('Service', 'CirclePinService');

/**
 * Circles Controller
 *
 * @property Circle $Circle
 */
class CirclesController extends AppController
{
    public $uses = [
        'Circle'
    ];
    public $components = [
        'Mention'
    ];
    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
    }


    /**
     * Display circle creation form
     * @return CakeResponse
     */
    public function create()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    /**
     * add method
     *
     * @return void
     */
    public function add()
    {
        $this->request->allowMethod('post');

        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');

        $userId = $this->Auth->user('id');
        $data = $this->request->data;

        // extract adding member ids
        $memberIds = [];
        $members = Hash::get($data, 'Circle.members');
        if ($members) {
            $memberIds = $CircleService->extractUserIds($members);
            unset($data['Circle']['members']);
        }

        // validate circle
        if ($CircleService->validateCreate($data, $userId) !== true) {
            $this->Notification->outError(__("Failed to create a circle."));
            return $this->redirect($this->referer());
        }

        // create circle and add members
        if (!$CircleService->create($data, $userId, $memberIds)) {
            $this->Notification->outError(__("Failed to create a circle."));
            return $this->redirect($this->referer());
        }

        $circleId = $this->Circle->getLastInsertID();
        // Notification
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_ADD_USER, $circleId,
            null, $memberIds);
        $this->_updateSetupStatusIfNotCompleted();

        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect("/circles/${circleId}/posts");
    }

    /**
     * Display circle edit form
     * @return CakeResponse
     */
    public function edit(int $circleId)
    {
        if(!$this->Circle->CircleMember->isAdmin($this->Auth->User('id'), $circleId)) {
            $this->Notification->outError(__("You have no right to operate it."));
            return $this->redirect($this->referer());
        }
        $this->set('circleId', $circleId);
        $this->layout = LAYOUT_ONE_COLUMN;

        $this->request->data = $this->Circle->findById($circleId);
        $this->request->data['Circle']['members'] = null;

        $tab = $this->request->query('type') ?? "";
        if (!in_array($tab,  ['memberList', 'addMembers'], true)) {
            $tab = '';
        }
        $this->set('tab', $tab);

        $circle_members = $this->Circle->CircleMember->getMembers($circleId, true);
        $this->set('circle_members', $circle_members);

        return $this->render();
    }

    function ajax_select2_init_circle_members($circle_id)
    {
        $this->_ajaxPreProcess();
        $res = $this->Circle->CircleMember->getCircleInitMemberSelect2($circle_id);
        return $this->_ajaxGetResponse($res);
    }

    /**
     * select2のユーザ検索 （サークルメンバー追加用）
     * サークル未参加のユーザーリストを返す
     */
    function ajax_select2_non_circle_member()
    {
        $this->_ajaxPreProcess();
        $circle_id = $this->request->params['named']['circle_id'];
        $query = $this->request->query;
        $res = [];
        if (isset($query['term']) && $query['term'] && isset($query['page_limit']) && $query['page_limit']) {
            $res = $this->Circle->CircleMember->getNonCircleMemberSelect2($circle_id, $query['term'],
                $query['page_limit'], true);
        }
        return $this->_ajaxGetResponse($res);
    }

    /**
     * select2のサークル名検索
     */
    function ajax_select2_circles()
    {
        $this->_ajaxPreProcess();
        $query = $this->request->query;
        $res = ['results' => []];
        if (isset($query['term']) && $query['term'] && count($query['term']) <= SELECT2_QUERY_LIMIT && isset($query['page_limit']) && $query['page_limit']) {
            $res = $this->Circle->getAccessibleCirclesSelect2($query['term'], $query['page_limit']);
        }
        if (isset($query['in_post_id']) && !empty($query['in_post_id'])) {
            $res['results'] = $this->Mention::filterAsMentionableCircle($query['in_post_id'], $res['results']);
        }
        return $this->_ajaxGetResponse($res);
    }

    /**
     * サークル基本情報更新
     */
    public function update()
    {
        $this->request->allowMethod('put');
        $this->Circle->id = $this->request->params['named']['circle_id'];
        try {
            if (!$this->Circle->exists()) {
                throw new RuntimeException(__("This circle does not exist."));
            }
            if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $this->Circle->id)) {
                throw new RuntimeException(__("It's only a circle administrator that can change circle settings."));
            }
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            $this->redirect($this->referer());
            return;
        }
        $before_circle = $this->Circle->read();
        // team_all_flg, public_flg は変更不可
        $this->request->data['Circle']['team_all_flg'] = $before_circle['Circle']['team_all_flg'];
        $this->request->data['Circle']['public_flg'] = $before_circle['Circle']['public_flg'];

        if (!$this->Circle->edit($this->request->data)) {
            $this->Notification->outError(__("Failed to save circle settings."));
            $this->redirect($this->referer());
            return;
        }
        return $this->redirect("/circles/".$this->Circle->id."/about");
    }

    /**
     * サークルメンバー追加
     */
    public function add_member()
    {
        $this->request->allowMethod('put');

        /** @var ExperimentService $ExperimentService */
        $CircleService = ClassRegistry::init('CircleService');

        $circleId = Hash::get($this->request->data, 'Circle.id');
        $userId = $this->Auth->user('id');

        // extract adding member ids
        $members = explode(",", Hash::get($this->request->data, 'Circle.members'));
        $memberIds = Hash::map($members, '', function ($member) {
            $memberUserId = str_replace('user_', '', $member);
            return $memberUserId;
        });

        // validation
        $validateAddMembers = $CircleService->validateAddMembers($circleId, $userId, $memberIds);
        if ($validateAddMembers !== true) {
            $this->Notification->outError($validateAddMembers);
            return $this->redirect($this->referer());
        }

        // add members
        if (!$CircleService->addMembers($circleId, $memberIds)) {
            $this->Notification->outError(__("Failed to add circle member(s.)"));
            $this->redirect($this->referer());
        }

        // Notification
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_ADD_USER, $circleId,
            null, $memberIds);

        $this->Notification->outSuccess(__("Added circle member(s)."));
        $this->redirect($this->referer());
    }

    public function delete()
    {
        $this->Circle->id = $this->request->params['named']['circle_id'];
        try {
            if (!$this->Circle->exists()) {
                throw new RuntimeException(__("This circle does not exist."));
            }
            if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $this->Circle->id)) {
                throw new RuntimeException(__("It's only a circle administrator that can delete a circle."));
            }
            $teamAllCircle = $this->Circle->getTeamAllCircle();
            if (isset($teamAllCircle["Circle"]["id"]) &&
                $teamAllCircle["Circle"]["id"] == $this->Circle->id
            ) {
                throw new RuntimeException(__("The all team circle can not be deleted."));
            }
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $this->request->allowMethod('post');
        $this->Circle->delete();
        //サークル削除時はユーザ数によってキャッシュ削除の処理が重くなるため、user_data全て削除
        Cache::clear(false, 'user_data');
        $this->Notification->outSuccess(__("Deleted a circle."));
        $this->redirect('/');
    }

    public function ajax_get_public_circles_modal()
    {
        $this->_ajaxPreProcess();
        // 参加済サークル（公開 + 秘密）
        $joined_circles = array_merge(
            $this->Circle->CircleMember->getMyCircle([
                'circle_created_start' => strtotime("-1 week"),
                'order'                => ['Circle.created desc'],
            ]),
            $this->Circle->CircleMember->getMyCircle([
                'circle_created_end' => strtotime("-1 week"),
                'order'              => ['Circle.modified desc'],
            ])
        );
        // 参加済サークルのメンバー数をまとめて取得
        $joined_circle_count_list = $this->Circle->CircleMember->getActiveMemberCountList(Hash::extract($joined_circles,
            "{n}.Circle.id"));

        $non_joined_circles = array_merge(
            $this->Circle->getPublicCircles('non-joined', strtotime("-1 week"), null, 'Circle.created desc'),
            $this->Circle->getPublicCircles('non-joined', null, strtotime("-1 week"), 'Circle.modified desc')
        );
        // チーム全体サークルを先頭に移動する
        foreach ($joined_circles as $k => $circle) {
            if ($circle['Circle']['team_all_flg']) {
                $team_all_circle = array_splice($joined_circles, $k, 1);
                array_unshift($joined_circles, $team_all_circle[0]);
                break;
            }
        }
        $this->set(compact('joined_circles', 'non_joined_circles', 'joined_circle_count_list'));
        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_public_circles');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    /**
     * サークルの 参加/不参加 切り替え
     *
     * @return CakeResponse
     */
    public function ajax_join_circle()
    {
        $this->request->allowMethod('post');
        $this->_ajaxPreProcess();

        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');

        $toJoin = Hash::get($this->request->data, 'Circle.0.join');
        $circleId = Hash::get($this->request->data, 'Circle.0.circle_id');
        $userId = $this->Auth->user('id');

        // Leave circle
        if (!$toJoin) {
            $isLeaved = $CircleService->removeCircleMember($this->current_team_id, $circleId, $userId);
            if ($isLeaved) {
                return $this->_ajaxGetResponse(['msg' => __("Leave a circle.")]);
            } else {
                return $this->_ajaxGetResponse(['msg' => __("Failed to change circle belonging status.")]);
            }
        }

        // Join circle
        $isJoined = $CircleService->join($circleId, $this->Auth->user('id'));
        if (!$isJoined) {
            return $this->_ajaxGetResponse(['msg' => __("Failed to change circle belonging status.")]);
        }

        $this->_updateSetupStatusIfNotCompleted();

        // Notify to circle member
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_USER_JOIN, $circleId);

        return $this->_ajaxGetResponse(['msg' => __("Join a circle.")]);
    }

    public function ajax_get_circle_members()
    {
        $circle_id = $this->request->params['named']['circle_id'];
        $this->_ajaxPreProcess();
        $circle_members = $this->Circle->CircleMember->getMembers($circle_id, true, 'CircleMember.modified', 'desc');
        $this->set(compact('circle_members', 'circle_id'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_circle_members');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    /**
     * メンバーをサークル管理者に設定/解除
     */
    public function ajax_edit_admin_status()
    {
        $this->request->allowMethod('post');
        $this->_ajaxPreProcess();

        // validate
        $this->Circle->id = $this->request->params['named']['circle_id'];
        if (!$this->Circle->exists()) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__("This circle does not exist.")));
        }
        if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $this->Circle->id)) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__("It's only a circle administrator that can change circle settings.")));
        }
        // 最後の管理者を外そうとした場合
        $admin_list = $this->Circle->CircleMember->getAdminMemberList($this->Circle->id, true);
        if ($this->request->data['CircleMember']['admin_flg'] == "0" && count($admin_list) == 1) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__("One or more members must be assigned as circle administrators.")));
        }

        // 管理者ステータス変更処理
        $res = $this->Circle->CircleMember->editAdminStatus($this->Circle->id,
            $this->request->data['CircleMember']['user_id'],
            $this->request->data['CircleMember']['admin_flg']);
        // 処理失敗
        if (!$res) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__("An error occurred while processing.")));
        }

        Cache::delete($this->Circle->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true,
            $this->request->data['CircleMember']['user_id']), 'user_data');

        // 処理成功
        $result = [
            'error'       => false,
            'result'      => [
                'user_id'   => $this->request->data['CircleMember']['user_id'],
                'admin_flg' => $this->request->data['CircleMember']['admin_flg'],
            ],
            'self_update' => ($this->Auth->user('id') == $this->request->data['CircleMember']['user_id']) ? true : false,
            'message'     => [
                'title' => __("Success"),
                'text'  => $this->request->data['CircleMember']['admin_flg']
                    ? __("Succeeded to set the administrator.")
                    : __("Succeeded to remove from the administrator."),
            ],
        ];
        return $this->_ajaxGetResponse($result);
    }

    /**
     * メンバーをサークルから外す
     */
    public function ajax_leave_circle()
    {
        $this->request->allowMethod('post');
        $this->_ajaxPreProcess();
        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init("CircleService");
        /** @var CirclePinService $CirclePinService */
        $CirclePinService = ClassRegistry::init("CirclePinService");

        // validate
        $this->Circle->id = $this->request->params['named']['circle_id'];
        if (!$this->Circle->exists()) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__("This circle does not exist.")));
        }
        if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $this->Circle->id)) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__("It's only a circle administrator that can change circle settings.")));
        }
        $admin_list = $this->Circle->CircleMember->getAdminMemberList($this->Circle->id, true);
        if ($this->request->data['CircleMember']['user_id'] == end($admin_list) && count($admin_list) == 1) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__("One or more members must be assigned as circle administrators.")));
        }

        // サークルから外す処理
        $res = $CircleService->removeCircleMember($this->current_team_id, $this->Circle->id, $this->request->data['CircleMember']['user_id']);
        // Remove and update circle pin information
        if($res){
            $res = $CirclePinService->deleteCircleId($this->request->data['CircleMember']['user_id'], $this->current_team_id, $this->Circle->id);
        }
        // 処理失敗
        if (!$res) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__("An error occurred while processing.")));
        }
        Cache::delete($this->Circle->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true,
            $this->request->data['CircleMember']['user_id']), 'user_data');

        // 処理成功
        $result = [
            'error'       => false,
            'result'      => [
                'user_id' => $this->request->data['CircleMember']['user_id']
            ],
            'self_update' => ($this->Auth->user('id') == $this->request->data['CircleMember']['user_id']) ? true : false,
            'message'     => [
                'title' => __("Success"),
                'text'  => __("Removed from the circle members."),
            ],
        ];
        return $this->_ajaxGetResponse($result);
    }

    /**
     * サークル設定変更 モーダル表示
     *
     * @return CakeResponse
     */
    public function ajax_setting()
    {
        $this->_ajaxPreProcess();

        // このサークルに属しているかチェック
        $circle_member = $this->Circle->CircleMember->isBelong($this->request->params['named']['circle_id'],
            $this->Auth->user('id'));
        if (!$circle_member) {
            throw new NotFoundException(__("This circle does not exist."));
        }
        $this->set('circle_member', $circle_member);

        $response = $this->render('Feed/modal_circle_setting');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    /**
     * サークル設定変更
     *
     * @return CakeResponse
     */
    public function ajax_change_setting()
    {
        $this->_ajaxPreProcess();
        $this->request->allowMethod('post');

        // このサークルに属しているかチェック
        $circle_member = $this->Circle->CircleMember->isBelong($this->request->data('CircleMember.circle_id'),
            $this->Auth->user('id'));
        if (!$circle_member) {
            throw new NotFoundException(__("This circle does not exist."));
        }

        // 設定データ更新
        $res = $this->Circle->CircleMember->editCircleSetting($this->request->data('CircleMember.circle_id'),
            $this->Auth->user('id'),
            $this->request->data);
        $error = $res ? false : true;
        $msg = $error ? __("An error has occurred.") : __("Update setting.");

        return $this->_ajaxGetResponse(['error' => $error, 'msg' => $msg]);
    }

    /**
     * ajax エラー用レスポンスデータを返す
     *
     * @param $message
     *
     * @return array
     */
    private function _makeEditErrorResult($message)
    {
        return [
            'error'       => true,   // エラーの有無
            'result'      => [],     // 更新されたデータ
            'self_update' => false,  // 操作者自身が更新されたか
            'message'     => [
                'title' => __("Error"),
                'text'  => $message,
            ],
        ];
    }
}
