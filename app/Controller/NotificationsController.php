<?php
App::uses('AppController', 'Controller');

/**
 * Notification Controller
 */
class NotificationsController extends AppController
{

    public $components = ['NotifyBiz'];

    /**
     * @return array
     */
    public function index()
    {
        $limit = 20;
        $this->_setViewValOnRightColumn();
        $notify_items = $this->NotifyBiz->getNotification($limit);
        $this->set(compact('notify_items'));
    }

    /**
     * @param $oldest_score_id
     *
     * @return array
     */
    public function ajax_get_old_notify_more($oldest_score_id)
    {
        $this->_ajaxPreProcess();
        $limit = 20;
        $notify_items = $this->NotifyBiz->getNotification($limit, $oldest_score_id);
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
        $notify_items = $this->NotifyBiz->getNotification();
        $this->set(compact('notify_items'));
        $response = $this->render('Notification/notify_items');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

}
