<?php

/**
 * JqueryプラグインのPnotifyを使ってflashメッセージを表示する
 * Class PnotifyComponent
 *
 * @property SessionComponent $Session
 */
class PnotifyComponent extends Component
{

    public $name = "Pnotify";

    /**
     * @var AppController
     */
    private $Controller;

    public $components = [
        'Session'
    ];

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

    public function initialize(Controller $controller)
    {
        $this->Controller = $controller;
        $this->_setDefaultOption();
    }

    private function _setDefaultOption()
    {
        $this->options[self::TYPE_INFO]['title'] = __d('notify', "お知らせ");
        $this->options[self::TYPE_SUCCESS]['title'] = __d('notify', "成功");
        $this->options[self::TYPE_NOTICE]['title'] = __d('notify', "注意");
        $this->options[self::TYPE_ERROR]['title'] = __d('notify', "エラー");
    }

    /**
     * 成功のflashメッセージ出力
     *
     * @param       $message
     * @param array $option
     */
    public function outSuccess($message, $option = [])
    {
        $this->out(self::TYPE_SUCCESS, $message, $option);
    }

    /**
     * お知らせのflashメッセージ出力
     *
     * @param       $message
     * @param array $option
     */
    public function outInfo($message, $option = [])
    {
        $this->out(self::TYPE_INFO, $message, $option);
    }

    /**
     * 注意のflashメッセージ出力
     *
     * @param       $message
     * @param array $option
     */
    public function outNotice($message, $option = [])
    {
        $this->out(self::TYPE_NOTICE, $message, $option);
    }

    /**
     * エラーのflashメッセージ出力
     *
     * @param       $message
     * @param array $option
     */
    public function outError($message, $option = [])
    {
        $this->out(self::TYPE_ERROR, $message, $option);
    }

    /**
     * flashメッセージを表示
     *
     * @param       $type
     * @param       $message
     * @param array $option
     */
    public function out($type, $message, $option = [])
    {
        if (!array_key_exists($type, $this->options) || !$message) {
            return;
        }
        $option['escape'] = false;
        $merged_option = array_merge($this->options[$type], $option);
        //改行を<br>に変換
        $message = nl2br(h($message));
        //改行を除去
        $cr = array("\r\n", "\r", "\n");
        $message = str_replace($cr, "", $message);
        $this->Session->setFlash($message, 'flash_pnotify', $merged_option, 'pnotify');
    }

}