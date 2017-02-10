<?php
App::uses('AppController', 'Controller');

/**
 * Circles Controller
 *
 * @property Circle $Circle
 */
class CirclesController extends AppController
{
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
     * add method
     *
     * @return void
     */
    public function add()
    {
        $this->request->allowMethod('post');
        $this->Circle->create();
        App::import('Service', 'ExperimentService');
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init('ExperimentService');
        if ($ExperimentService->isDefined(Experiment::NAME_CIRCLE_DEFAULT_SETTING_OFF)) {
            $addCircleSuccess = $this->Circle->add($this->request->data, false, false);
        } else {
            $addCircleSuccess = $this->Circle->add($this->request->data);
        }

        if ($addCircleSuccess) {
            if (!empty($this->Circle->add_new_member_list)) {
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_ADD_USER, $this->Circle->id,
                    null, $this->Circle->add_new_member_list);
            }
            $this->updateSetupStatusIfNotCompleted();
            $this->Pnotify->outSuccess(__("Created a circle."));
        } else {
            $this->Pnotify->outError(__("Failed to create a circle."));
        }
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function ajax_get_edit_modal()
    {
        $circle_id = $this->request->params['named']['circle_id'];
        $this->_ajaxPreProcess();
        $this->request->data = $this->Circle->findById($circle_id);
        $this->request->data['Circle']['members'] = null;

        $circle_members = $this->Circle->CircleMember->getMembers($circle_id, true);
        $this->set('circle_members', $circle_members);
        //htmlレンダリング結果
        $response = $this->render('modal_edit_circle');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
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
        return $this->_ajaxGetResponse($res);
    }

    /**
     * サークル基本情報更新
     */
    public function edit()
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
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
            return;
        }
        $before_circle = $this->Circle->read();
        // team_all_flg, public_flg は変更不可
        $this->request->data['Circle']['team_all_flg'] = $before_circle['Circle']['team_all_flg'];
        $this->request->data['Circle']['public_flg'] = $before_circle['Circle']['public_flg'];

        if ($this->Circle->edit($this->request->data)) {
            $this->Pnotify->outSuccess(__("Saved circle settings."));
        } else {
            $this->Pnotify->outError(__("Failed to save circle settings."));
        }
        $this->redirect($this->referer());
    }

    /**
     * サークルメンバー追加
     */
    public function add_member()
    {
        $this->request->allowMethod('put');
        $this->Circle->id = $this->request->params['named']['circle_id'];
        $before_circle = null;
        try {
            if (!$this->Circle->exists()) {
                throw new RuntimeException(__("This circle does not exist."));
            }
            if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $this->Circle->id)) {
                throw new RuntimeException(__("It's only a circle administrator that can change circle settings."));
            }
            $before_circle = $this->Circle->read();
            if ($before_circle['Circle']['team_all_flg']) {
                throw new RuntimeException(__("You can't change members of the all team circle."));
            }
            $this->request->data['Circle']['team_all_flg'] = $before_circle['Circle']['team_all_flg'];
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
            return;
        }

        App::import('Service', 'ExperimentService');
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init('ExperimentService');

        // サークルにメンバー追加
        if ($ExperimentService->isDefined(Experiment::NAME_CIRCLE_DEFAULT_SETTING_OFF)) {
            $isAddedMember = $this->Circle->addMember($this->request->data, false, false);
        } else {
            $isAddedMember = $this->Circle->addMember($this->request->data);
        }

        // サークル参加通知 & レスポンスメッセージ定義
        if ($isAddedMember) {
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_ADD_USER, $this->Circle->id,
                null, $this->Circle->add_new_member_list);
            $this->Pnotify->outSuccess(__("Add circle member(s)."));
        } else {
            $this->Pnotify->outError(__("Failed to add circle member(s.)"));
        }
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
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $this->request->allowMethod('post');
        $this->Circle->delete();
        //サークル削除時はユーザ数によってキャッシュ削除の処理が重くなるため、user_data全て削除
        Cache::clear(false, 'user_data');
        $this->Pnotify->outSuccess(__("Deleted a circle."));
        $this->redirect($this->referer());
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

        App::import('Service', 'ExperimentService');
        /** @var ExperimentService $ExperimentService */
        $ExperimentService = ClassRegistry::init('ExperimentService');

        $error = false;
        $msg = '';

        // サークル参加/不参加ステータス変更
        if ($ExperimentService->isDefined(Experiment::NAME_CIRCLE_DEFAULT_SETTING_OFF)) {
            $changedJoinedStatus = $this->Circle->CircleMember->joinCircle($this->request->data, false, false);
        } else {
            $changedJoinedStatus = $this->Circle->CircleMember->joinCircle($this->request->data);
        }
        if (!$changedJoinedStatus) {
            return $this->_ajaxGetResponse(['msg' => __("Failed to change circle belonging status.")]);
        }

        // サークル参加通知 & レスポンスメッセージ定義
        $msg = '';
        $newJoinedCircles = $this->Circle->CircleMember->new_joined_circle_list;
        if (!empty($newJoinedCircles)) {
            foreach ($newJoinedCircles as $circleId) {
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_USER_JOIN, $circleId);
            }
            $this->updateSetupStatusIfNotCompleted();
            $msg = __("Join a circle.");
        } else {
            $msg = __("Leave a circle.");
        }

        return $this->_ajaxGetResponse(['msg' => $msg]);
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
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__("An error occured while processing.")));
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
        $res = $this->Circle->CircleMember->unjoinMember($this->Circle->id,
            $this->request->data['CircleMember']['user_id']);
        // 処理失敗
        if (!$res) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__("An error occured while processing.")));
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
