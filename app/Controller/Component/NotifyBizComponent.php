<?php
App::uses('ModelType', 'Model');

/**
 * @author daikihirakata
 */
class NotifyBizComponent extends Object
{

    public $name = "NotifyBiz";

    /**
     * @var AppController
     */
    var $Controller;

    /**
     * @var SessionComponent
     */
    var $Session;

    /**
     * @var AuthComponent
     */
    var $Auth;

    /**
     * @var Notification
     */
    var $Notification;

    /**
     * @var NotifySetting
     */
    var $NotifySetting;

    function initialize()
    {
    }

    function startup(&$controller)
    {
        $this->Controller = $controller;
        $this->Auth = $this->Controller->Auth;
        $this->Session = $this->Controller->Session;

        ClassRegistry::init('Notification');
        $this->Notification = new Notification();
        ClassRegistry::init('NotifySetting');
        $this->NotifySetting = new NotifySetting();
    }

    function beforeRender()
    {
    }

    function shutdown()
    {
    }

    function beforeRedirect()
    {
    }
//
//    function notifiActionDelete($id, $to_user_id, $goal_id, $action_name)
//    {
//        if (!$id || !$to_user_id || !$goal_id || !$action_name) {
//            return false;
//        }
//        if (!$this->NotifiSetting->isOnNotifi('del_my_action_notifi_flg', $to_user_id)) {
//            return false;
//        }
//        $data = array(
//            'Notification' => array(
//                'team_id'       => $this->Session->read('team_id'),
//                'user_id'       => $to_user_id,
//                'form_id'       => TYPE_NOTICE_FORM_ACTION_DELETE,
//                'from_user_id'  => $this->Auth->user('id'),
//                'goal_id'       => $goal_id,
//                'unread_flg'    => 1,
//                'model_type_id' => ModelType::$TYPE_ACTION,
//                'nb'            => $action_name,
//            ),
//        );
//        $save_notifi_changed = $this->Notification->saveAll($data);
//        return $save_notifi_changed;
//    }

}
