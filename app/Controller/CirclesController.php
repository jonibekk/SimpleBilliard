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

        if ($this->Circle->add($this->request->data)) {
            if (!empty($this->Circle->add_new_member_list)) {
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_ADD_USER, $this->Circle->id,
                                                 null, $this->Circle->add_new_member_list);
            }
            $this->Pnotify->outSuccess(__d('gl', "サークルを作成しました。"));
        }
        else {
            $this->Pnotify->outError(__d('gl', "サークルの作成に失敗しました。"));
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
                                                                          $query['page_limit']);
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
        $res = [];
        if (isset($query['term']) && $query['term'] && isset($query['page_limit']) && $query['page_limit']) {
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
                throw new RuntimeException(__d('gl', "このサークルは存在しません。"));
            }
            if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $this->Circle->id)) {
                throw new RuntimeException(__d('gl', "サークルの変更ができるのはサークル管理者のみです。"));
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
            $this->Pnotify->outSuccess(__d('gl', "サークル設定を保存しました。"));
        }
        else {
            $this->Pnotify->outError(__d('gl', "サークル設定の保存に失敗しました。"));
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
                throw new RuntimeException(__d('gl', "このサークルは存在しません。"));
            }
            if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $this->Circle->id)) {
                throw new RuntimeException(__d('gl', "サークルの変更ができるのはサークル管理者のみです。"));
            }
            $before_circle = $this->Circle->read();
            if ($before_circle['Circle']['team_all_flg']) {
                throw new RuntimeException(__d('gl', "チーム全体サークルは変更出来ません。"));
            }
            $this->request->data['Circle']['team_all_flg'] = $before_circle['Circle']['team_all_flg'];
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
            return;
        }

        if ($this->Circle->addMember($this->request->data)) {
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_ADD_USER, $this->Circle->id,
                                             null, $this->Circle->add_new_member_list);
            $this->Pnotify->outSuccess(__d('gl', "サークルメンバーを追加しました。"));
        }
        else {
            $this->Pnotify->outError(__d('gl', "サークルメンバーの追加に失敗しました。"));
        }
        $this->redirect($this->referer());
    }

    public function delete()
    {
        $this->Circle->id = $this->request->params['named']['circle_id'];
        try {
            if (!$this->Circle->exists()) {
                throw new RuntimeException(__d('gl', "このサークルは存在しません。"));
            }
            if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $this->Circle->id)) {
                throw new RuntimeException(__d('gl', "サークルの削除ができるのはサークル管理者のみです。"));
            }
            $teamAllCircle = $this->Circle->getTeamAllCircle();
            if (isset($teamAllCircle["Circle"]["id"]) &&
                $teamAllCircle["Circle"]["id"] == $this->Circle->id
            ) {
                throw new RuntimeException(__d('gl', "チーム全体サークルは削除できません。"));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $this->request->allowMethod('post');
        $this->Circle->delete();
        //サークル削除時はユーザ数によってキャッシュ削除の処理が重くなるため、user_data全て削除
        Cache::clear(false, 'user_data');
        $this->Pnotify->outSuccess(__d('gl', "サークルを削除しました。"));
        $this->redirect($this->referer());
    }

    public function ajax_get_public_circles_modal()
    {
        $this->_ajaxPreProcess();
        // 参加済サークル（公開 + 秘密）
        $joined_circles = array_merge(
            $this->Circle->CircleMember->getMyCircle(['circle_created_start' => strtotime("-1 week"),
                                                      'order'                => ['Circle.created desc'],
                                                     ]),
            $this->Circle->CircleMember->getMyCircle(['circle_created_end' => strtotime("-1 week"),
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

        $msg = null;
        if ($this->Circle->CircleMember->joinCircle($this->request->data)) {
            if (!empty($this->Circle->CircleMember->new_joined_circle_list)) {
                foreach ($this->Circle->CircleMember->new_joined_circle_list as $circle_id) {
                    $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_USER_JOIN, $circle_id);
                }
                $msg = __d('gl', "サークルに参加しました。");
            }
            else {
                $msg = __d('gl', "サークルから外れました。");
            }
        }
        else {
            $msg = __d('gl', "サークルの参加設定の保存に失敗しました。");
        }
        return $this->_ajaxGetResponse(['msg' => $msg]);
    }

    public function ajax_get_circle_members()
    {
        $circle_id = $this->request->params['named']['circle_id'];
        $this->_ajaxPreProcess();
        $circle_members = $this->Circle->CircleMember->getMembers($circle_id, true, 'CircleMember.modified', 'desc');
        $this->set(compact('circle_members'));

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
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__d('gl', "このサークルは存在しません。")));
        }
        if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $this->Circle->id)) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__d('gl', "サークルの変更ができるのはサークル管理者のみです。")));
        }
        // 最後の管理者を外そうとした場合
        $admin_list = $this->Circle->CircleMember->getAdminMemberList($this->Circle->id, true);
        if ($this->request->data['CircleMember']['admin_flg'] == "0" && count($admin_list) == 1) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__d('gl', "サークルの管理者を１人以上設定する必要があります。")));
        }

        // 管理者ステータス変更処理
        $res = $this->Circle->CircleMember->editAdminStatus($this->Circle->id,
                                                            $this->request->data['CircleMember']['user_id'],
                                                            $this->request->data['CircleMember']['admin_flg']);
        // 処理失敗
        if (!$res) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__d('gl', "処理中にエラーが発生しました。")));
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
                'title' => __d('notify', "成功"),
                'text'  => $this->request->data['CircleMember']['admin_flg']
                    ? __d('gl', "管理者に設定しました。")
                    : __d('gl', "管理者から外しました。"),
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
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__d('gl', "このサークルは存在しません。")));
        }
        if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $this->Circle->id)) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__d('gl', "サークルの変更ができるのはサークル管理者のみです。")));
        }
        $admin_list = $this->Circle->CircleMember->getAdminMemberList($this->Circle->id, true);
        if ($this->request->data['CircleMember']['user_id'] == end($admin_list) && count($admin_list) == 1) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__d('gl', "サークルの管理者を１人以上設定する必要があります。")));
        }

        // サークルから外す処理
        $res = $this->Circle->CircleMember->unjoinMember($this->Circle->id,
                                                         $this->request->data['CircleMember']['user_id']);
        // 処理失敗
        if (!$res) {
            return $this->_ajaxGetResponse($this->_makeEditErrorResult(__d('gl', "処理中にエラーが発生しました。")));
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
                'title' => __d('notify', "成功"),
                'text'  => __d('gl', "サークルから外しました。"),
            ],
        ];
        return $this->_ajaxGetResponse($result);
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
                'title' => __d('notify', "エラー"),
                'text'  => $message,
            ],
        ];
    }
}
