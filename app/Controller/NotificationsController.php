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
        $this->_setViewValOnRightColumn();
        $notify_items = $this->NotifyBiz->getNotification(NOTIFY_PAGE_ITEMS_NUMBER);
        $isExistMoreNotify = true;
        if(count($notify_items) < NOTIFY_PAGE_ITEMS_NUMBER) {
            $isExistMoreNotify = false;
        }
        $this->set(compact('notify_items', 'isExistMoreNotify'));
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
        if(count($notify_items) === 0) {
            return $this->_ajaxGetResponse("");
        }
        $this->set(compact('notify_items'));
        $response = $this->render('Notification/notify_items');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    /**
     * @return array
     */
    public function ajax_get_new_notify_count()
    {
        $this->_ajaxPreProcess();
        $notify_count = $this->NotifyBiz->getCountNewNotification();
        return $this->_ajaxGetResponse($notify_count);
    }

    /**
     * @return array
     */
    public function ajax_get_latest_notify_items()
    {
        $this->_ajaxPreProcess();
        $notify_items = $this->NotifyBiz->getNotification(NOTIFY_BELL_BOX_ITEMS_NUMBER);
        $this->set(compact('notify_items'));
        $response = $this->render('Notification/notify_items_in_list_box');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

}
