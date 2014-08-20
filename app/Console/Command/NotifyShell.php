<?php
App::uses('Controller', 'Core');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('SessionComponent', 'Controller/Component');
App::uses('NotifyBizComponent', 'Controller/Component');

/**
 * SendMailShell
 *
 * @property Team                                $Team
 * @property User                                $User
 * @property SessionComponent                    $Session
 * @property NotifyBizComponent                  $NotifyBiz
 */
class NotifyShell extends AppShell
{

    public $uses = array(
        'Team',
        'User',
        'SendMail'
    );
    public $components;
    /**
     * @var AppController
     */
    public $AppController;

    public function startup()
    {
        parent::startup();
        if ($this->params['session_id']) {
            CakeSession::id($this->params['session_id']);
            CakeSession::start();
        }
        $this->components = new ComponentCollection();
        $this->AppController = new AppController();
        $this->NotifyBiz = new NotifyBizComponent($this->components);
        $this->components->disable('Security');
    }

    public function __destruct()
    {
        unset($this->components);
        unset($this->AppController);
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $options = [
            'type'       => ['short' => 't', 'help' => '通知タイプ', 'required' => true,],
            'session_id' => ['short' => 's', 'help' => 'セッションID', 'required' => true,],
            'model_id'   => ['short' => 'm', 'help' => 'モデルID', 'required' => false,],
            'user_list' => ['short' => 'u', 'help' => '送信先ユーザリスト', 'required' => false,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        $to_user_list = null;
        if ($this->params['user_list']) {
            $to_user_list = json_decode(base64_decode($this->params['user_list']), true);
        }
        $this->NotifyBiz->sendNotify($this->params['type'], $this->params['model_id'], $to_user_list);
    }

}
