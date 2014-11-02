<?php
App::uses('AppController', 'Controller');

/**
 * Helps Controller
 *
 * @property Circle $Circle
 */
class HelpsController extends AppController
{

    //モデル使わない
//    var $uses = null;

    const TYPE_CREATE_GOAL_STEP01 = 0;
    const TYPE_CREATE_GOAL_STEP02 = 1;

    public $type = [
        self::TYPE_CREATE_GOAL_STEP01 => null,
        self::TYPE_CREATE_GOAL_STEP02 => null,
    ];

    private $default_type = [
        'title'        => null,
        'picture_name' => null,
        'body'         => null,
    ];

    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_setType();
    }

    private function _setType()
    {
        foreach ($this->type as $key => $val) {
            $this->type[$key] = $this->default_type;
        }
        $this->type[self::TYPE_CREATE_GOAL_STEP01]['title'] = __d('gl', "1 目的を決める");
        $this->type[self::TYPE_CREATE_GOAL_STEP01]['picture_name'] = "purpose-is.jpg";
        $this->type[self::TYPE_CREATE_GOAL_STEP02]['title'] = __d('gl', "2 基準を定める");
        $this->type[self::TYPE_CREATE_GOAL_STEP02]['picture_name'] = "goal-is.jpg";

    }

    function ajax_get_modal($type)
    {
        $this->_ajaxPreProcess();
        $html = null;
        if (isset($this->type[$type])) {
            $help_item = $this->type[$type];
            $this->set(compact('help_item'));
            $response = $this->render('modal_help');
            $html = $response->__toString();
        }
        return $this->_ajaxGetResponse($html);
    }

}
