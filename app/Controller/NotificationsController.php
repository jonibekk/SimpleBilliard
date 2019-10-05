<?php
App::uses('AppController', 'Controller');

/**
 * Notification Controller
 */
class NotificationsController extends AppController
{

    /**
     * @deprecated
     * @return array
     */
    public function index()
    {
        $this->NotifyBiz->resetCountNewNotification();
        $notify_items = $this->NotifyBiz->getNotification(NOTIFY_PAGE_ITEMS_NUMBER);
        $team = $this->Team->findById($this->current_team_id);
        $isExistMoreNotify = true;
        if (count($notify_items) < NOTIFY_PAGE_ITEMS_NUMBER) {
            $isExistMoreNotify = false;
        }
        $this->set(compact('notify_items', 'isExistMoreNotify', 'team'));
    }

    /**
     * @deprecated
     * お知らせを返すajax版
     *
     * @return CakeResponse
     */
    public function ajax_index()
    {
        $this->_ajaxPreProcess();

        $notify_items = $this->NotifyBiz->getNotification(NOTIFY_PAGE_ITEMS_NUMBER);
        $isExistMoreNotify = true;
        if (count($notify_items) < NOTIFY_PAGE_ITEMS_NUMBER) {
            $isExistMoreNotify = false;
        }
        $team = $this->Team->getCurrentTeam();
        $this->set(compact('notify_items', 'isExistMoreNotify','team'));
        $response = $this->render('/Notifications/index');

        $html = $response->__toString();
        $result = array(
            'html'     => $html,
            'item_cnt' => count($notify_items)
        );
        return $this->_ajaxGetResponse($result);
    }

    /**
     * 古いお知らせを返す（ページング表示用）
     *
     * @param        $oldest_score
     * @param string $location_type 呼び出し元を表す文字列
     *                              'page'     - すべてのお知らせページ
     *                              'dropdown' - ヘッダーのお知らせ一覧ポップアップ
     *
     * @return CakeResponse
     */
    public function ajax_get_old_notify_more($oldest_score, $location_type = 'page')
    {
        $this->_ajaxPreProcess();
        $limit = $location_type == 'page' ? NOTIFY_PAGE_ITEMS_NUMBER : NOTIFY_BELL_BOX_ITEMS_NUMBER;
        $notify_items = $this->NotifyBiz->getNotification($limit, $oldest_score);
        $team = $this->Team->findById($this->current_team_id);
        if (count($notify_items) === 0) {
            return $this->_ajaxGetResponse("");
        }
        $this->set(compact('notify_items', 'team', 'location_type'));
        $response = $this->render('Notification/notify_items');

        $html = $response->__toString();
        $result = array(
            'html'     => $html,
            'item_cnt' => count($notify_items)
        );
        return $this->_ajaxGetResponse($result);
    }

    /**
     * @return int
     */
    public function ajax_get_new_notify_count()
    {
        $this->_ajaxPreProcess();

        // URLパラメータとセッションのチームIDが違う場合はエラーにする
        if ($this->Session->read('current_team_id') != $this->request->params['named']['team_id']) {
            return $this->_ajaxGetResponse(['error' => 'invalid_team_id']);
        }

        $notify_count = 0;
        if ($this->Auth->user('id')) {
            $notify_count = $this->NotifyBiz->getCountNewNotification();
        }
        return $this->_ajaxGetResponse($notify_count);
    }

    /**
     * @return int
     */
    public function ajax_get_new_message_notify_count()
    {
        $this->_ajaxPreProcess();

        // URLパラメータとセッションのチームIDが違う場合はエラーにする
        if ($this->Session->read('current_team_id') != $this->request->params['named']['team_id']) {
            return $this->_ajaxGetResponse(['error' => 'invalid_team_id']);
        }

        $notify_count = 0;
        if ($this->Auth->user('id')) {
            $notify_count = $this->NotifyBiz->getCountNewMessageNotification();
        }
        return $this->_ajaxGetResponse($notify_count);
    }

    /**
     * @return array
     */
    public function ajax_get_latest_notify_items()
    {
        $this->_ajaxPreProcess();
        $this->NotifyBiz->resetCountNewNotification();
        $notify_items = $this->NotifyBiz->getNotification(NOTIFY_BELL_BOX_ITEMS_NUMBER);
        $team = $this->Team->findById($this->current_team_id);
        $this->set(compact('notify_items', 'team'));
        $response = $this->render('Notification/notify_items_in_list_box');
        $html = $response->__toString();
        $res = ['html' => $html, 'has_noti' => count($notify_items) > 0];
        return $this->_ajaxGetResponse($res);
    }

    /**
     * Message Notification (is not Bell Notification)
     * ※ This method might not be used anywhere
     * @return array
     */
    public function ajax_get_latest_message_notify_items()
    {
        $this->_ajaxPreProcess();
        $notify_items = $this->NotifyBiz->getMessageNotification(NOTIFY_BELL_BOX_ITEMS_NUMBER);
        $team = $this->Team->findById($this->current_team_id);
        $this->set(compact('notify_items', 'team'));
        $this->set("is_message_notify", true);
        $response = $this->render('Notification/message_notify_items_in_list_box');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    public function ajax_mark_all_read()
    {
        $this->_ajaxPreProcess();
        $notify_items = $this->NotifyBiz->getNotifyIds();
        foreach ($notify_items as $notify_id => $val) {
            $this->NotifyBiz->changeReadStatusNotification($notify_id);
        }
        return $this->_ajaxGetResponse([]);
    }
}
