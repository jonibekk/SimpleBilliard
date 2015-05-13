<?php
App::uses('AppController', 'Controller');

/**
 * Notification Controller
 */
class NotificationsController extends AppController
{

    /**
     * @return array
     */
    public function index()
    {
        $this->_setMyCircle();
        $this->_setViewValOnRightColumn();
        $notify_items = $this->NotifyBiz->getNotification(NOTIFY_PAGE_ITEMS_NUMBER);
        $team = $this->Team->findById($this->current_team_id);
        $isExistMoreNotify = true;
        if (count($notify_items) < NOTIFY_PAGE_ITEMS_NUMBER) {
            $isExistMoreNotify = false;
        }
        $this->set(compact('notify_items', 'isExistMoreNotify', 'team'));
    }

    /**
     * @param $oldest_score
     *
     * @return array
     */
    public function ajax_get_old_notify_more($oldest_score)
    {
        $this->_ajaxPreProcess();
        $notify_items = $this->NotifyBiz->getNotification(NOTIFY_PAGE_ITEMS_NUMBER, $oldest_score);
        $team = $this->Team->findById($this->current_team_id);
        if (count($notify_items) === 0) {
            return $this->_ajaxGetResponse("");
        }
        $this->set(compact('notify_items', 'team'));
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
        $notify_count = 0;
        if ($this->Auth->user('id')) {
            $notify_count = $this->NotifyBiz->getCountNewNotification();
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
        return $this->_ajaxGetResponse($html);
    }

}
