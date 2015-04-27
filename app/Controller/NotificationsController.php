<?php
App::uses('AppController', 'Controller');

/**
 * Notification Controller
 *
 */

class NotificationsController extends AppController
{

    public $components = ['NotifyBiz'];

    /**
     * @return array
     */
    public function index()
    {
        return [];
    }

    /**
     * @param $oldest_score_id
     *
     * @return array
     */
    public function ajax_get_old_notify_more($oldest_score_id)
    {
        $this->_ajaxPreProcess();
        // rendering
        $html = $oldest_score_id;
        return $this->_ajaxGetResponse($html);
    }

    /**
     *
     * @return array
     */
    public function ajax_get_new_notify_count()
    {
        $this->_ajaxPreProcess();
        // rendering
        $html = 3;
        return $this->_ajaxGetResponse($html);
    }

    /**
     * @return array
     */
    public function ajax_get_latest_notify_items()
    {
        $this->_ajaxPreProcess();
        // rendering
        $html = "";
        return $this->_ajaxGetResponse($html);
    }

}
