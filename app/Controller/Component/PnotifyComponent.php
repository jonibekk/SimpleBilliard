<?php

/**
 * JqueryプラグインのPnotifyを使ってflashメッセージを表示する
 * Class PnotifyComponent
 */
class PnotifyComponent extends Object
{

    public $name = "Pnotify";

    /**
     * @var AppController
     */
    private $Controller;

    /**
     * @var SessionComponent
     */
    private $Session;

    /**
     * タイプ
     */
    const TYPE_INFO = 1;
    const TYPE_SUCCESS = 2;
    const TYPE_NOTICE = 3;
    const TYPE_ERROR = 4;

    private $options = [
        self::TYPE_INFO    => [
            'title' => null,
            'icon'  => 'fa fa-info-circle',
            'type'  => 'info',
        ],
        self::TYPE_SUCCESS => [
            'title' => null,
            'icon'  => 'fa fa-check-circle',
            'type'  => 'success',
        ],
        self::TYPE_NOTICE  => [
            'title' => null,
            'icon'  => 'fa fa-exclamation-circle',
            'type'  => 'notice',
        ],
        self::TYPE_ERROR   => [
            'title' => null,
            'icon'  => 'fa fa-exclamation-triangle',
            'type'  => 'error',
        ],
    ];

    function initialize()
    {
    }

    function startup(&$controller)
    {
        $this->Controller = $controller;
        $this->Session = $this->Controller->Session;
        $this->_setDefaultOption();
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

    private function _setDefaultOption()
    {
        $this->options[self::TYPE_INFO]['title'] = __d('notify', "お知らせ");
        $this->options[self::TYPE_SUCCESS]['title'] = __d('notify', "成功");
        $this->options[self::TYPE_NOTICE]['title'] = __d('notify', "注意");
        $this->options[self::TYPE_ERROR]['title'] = __d('notify', "エラー");
    }

    /**
     * flashメッセージを表示
     *
     * @param $type
     * @param $message
     */
    public function out($type, $message)
    {
        if (!array_key_exists($type, $this->options) || !$message) {
            return;
        }
        $this->Session->setFlash($message, 'flash_pnotify', $this->options[$type], 'pnotify');
    }

}