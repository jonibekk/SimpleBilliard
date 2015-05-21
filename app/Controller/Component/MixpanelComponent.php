<?php

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/05/28
 * Time: 0:36
 */
class MixpanelComponent extends Object
{

    const TRACK_CREATE_GOAL = 'CreGoal';
    const TRACK_DELETE_GOAL = 'DelGoal';
    const TRACK_UPDATE_GOAL = 'UpdGoal';
    const TRACK_COLLABORATE_GOAL = 'Clb';
    const TRACK_WITHDRAW_COLLABORATE = "WidClb";
    const TRACK_FOLLOW_GOAL = 'FolGoal';
    const TRACK_UN_FOLLOW_GOAL = 'UnFolGoal';
    const TRACK_CREATE_KR = 'CreKR';
    const TRACK_DELETE_KR = 'DelKR';
    const TRACK_UPDATE_KR = 'UpdKR';
    const TRACK_CREATE_ACTION = 'CreAct';
    const TRACK_DELETE_ACTION = 'DelAct';
    const TRACK_UPDATE_ACTION = 'UpdAct';
    const TRACK_POST = 'Post';
    const TRACK_COMMENT = 'Comment';
    const TRACK_LIKE = 'Like';
    const TRACK_APPROVAL = 'ApvAct';
    const TRACK_EVALUATION = 'EvaAct';
    const TRACK_2SV_ENABLE = '2SVEbl';
    const TRACK_2SV_DISABLE = '2SVDbl';

    public $name = "Mixpanel";

    /**
     * @var Mixpanel $MpOrigin
     */
    var $MpOrigin;

    /**
     * @var AppController $Controller
     */
    var $Controller;

    var $trackProperty = [];

    function initialize(&$controller)
    {
        $this->Controller = $controller;
        if (MIXPANEL_TOKEN) {
            $this->MpOrigin = Mixpanel::getInstance(MIXPANEL_TOKEN);
            if ($this->Controller->Auth->user()) {
                //mixpanelにユーザidをセット
                $this->MpOrigin->identify($this->Controller->Auth->user('id'));
                //チームIDをセット
                $this->MpOrigin->register('$team_id', $this->Controller->Session->read('current_team_id'));
            }
        }
    }

    function startup()
    {

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

    /**
     * ユーザ情報をセット
     *
     * @param $user_id
     */
    function setUser($user_id)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }

        $options = [
            'conditions' => ['User.id' => $user_id],
            'contain'    => ['PrimaryEmail',]
        ];
        $user = $this->Controller->User->find('first', $options);
        //ユーザ情報をセット
        $this->MpOrigin->people->set($user['User']['id'], [
            '$first_name'      => $user['User']['first_name'],
            '$last_name'       => $user['User']['last_name'],
            '$email'           => $user['PrimaryEmail']['email'],
            '$default_team_id' => $user['User']['default_team_id'],
            '$language'        => $user['User']['language'],
            '$is_admin'        => $user['User']['is_admin'],
            '$gender_id'       => $user['User']['gender_id'],
        ]);
    }

    function trackCreateGoal($goal_id)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->track(self::TRACK_CREATE_GOAL, ['$goal_id' => $goal_id]);
    }

    function trackCollaborateGoal($goal_id)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->track(self::TRACK_COLLABORATE_GOAL, ['$goal_id' => $goal_id]);
    }

    function trackFollowGoal($goal_id)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->track(self::TRACK_FOLLOW_GOAL, ['$goal_id' => $goal_id]);
    }

    function trackCreateKR($goal_id, $kr_id)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->track(self::TRACK_CREATE_KR, ['$goal_id' => $goal_id, '$kr_id' => $kr_id]);
    }

    function trackCreateAction($action_id, $goal_id = null, $kr_id = null)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->track(self::TRACK_CREATE_ACTION,
                               ['$action_id' => $action_id, '$goal_id' => $goal_id, '$kr_id' => $kr_id]);
    }

    /**
     * Add an array representing a message to be sent to Mixpanel to the in-memory queue.
     *
     * @param array $message
     */
    public function enqueue($message = [])
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->enqueue($message);
    }

    /**
     * Add an array representing a list of messages to be sent to Mixpanel to a queue.
     *
     * @param array $messages
     */
    public function enqueueAll($messages = [])
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->enqueueAll($messages);
    }

    /**
     * Flush the events queue
     *
     * @param int $desired_batch_size
     */
    public function flush($desired_batch_size = 50)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->flush($desired_batch_size);
    }

    /**
     * Empty the events queue
     */
    public function reset()
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->reset();
    }

    /**
     * Identify the user you want to associate to tracked events
     *
     * @param string|int $user_id
     */
    public function identify($user_id)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->identify($user_id);
    }

    /**
     * Track an event defined by $event associated with metadata defined by $properties
     *
     * @param string $event
     * @param array  $properties
     */
    public function track($event, $properties = [])
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->track($event, $properties);
    }

    /**
     * Register a property to be sent with every event.
     * If the property has already been registered, it will be
     * overwritten. NOTE: Registered properties are only persisted for the life of the Mixpanel class instance.
     *
     * @param string $property
     * @param mixed  $value
     */
    public function register($property, $value)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->register($property, $value);
    }

    /**
     * Register multiple properties to be sent with every event.
     * If any of the properties have already been registered,
     * they will be overwritten. NOTE: Registered properties are only persisted for the life of the Mixpanel class
     * instance.
     *
     * @param array $props_and_vals
     */
    public function registerAll($props_and_vals = [])
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->registerAll($props_and_vals);
    }

    /**
     * Register a property to be sent with every event.
     * If the property has already been registered, it will NOT be
     * overwritten. NOTE: Registered properties are only persisted for the life of the Mixpanel class instance.
     *
     * @param $property
     * @param $value
     */
    public function registerOnce($property, $value)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->registerOnce($property, $value);
    }

    /**
     * Register multiple properties to be sent with every event.
     * If any of the properties have already been registered,
     * they will NOT be overwritten. NOTE: Registered properties are only persisted for the life of the Mixpanel class
     * instance.
     *
     * @param array $props_and_vals
     */
    public function registerAllOnce($props_and_vals = [])
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->registerAllOnce($props_and_vals);
    }

    /**
     * Un-register an property to be sent with every event.
     *
     * @param string $property
     */
    public function unregister($property)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->unregister($property);
    }

    /**
     * Un-register a list of properties to be sent with every event.
     *
     * @param array $properties
     */
    public function unregisterAll($properties)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->unregisterAll($properties);
    }

    /**
     * Get a property that is set to be sent with every event
     *
     * @param string $property
     *
     * @return mixed
     */
    public function getProperty($property)
    {
        if (!MIXPANEL_TOKEN) {
            return null;
        }
        return $this->MpOrigin->getProperty($property);
    }

    /**
     * Alias an existing id with a different unique id. This is helpful when you want to associate a generated id
     * (such as a session id) to a user id or username.
     *
     * @param string|int $original_id
     * @param string|int $new_id
     */
    public function createAlias($original_id, $new_id)
    {
        if (!MIXPANEL_TOKEN) {
            return;
        }
        $this->MpOrigin->createAlias($original_id, $new_id);
    }

}
