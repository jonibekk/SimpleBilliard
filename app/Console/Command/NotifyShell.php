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
        $sessionId = viaIsSet($this->params['session_id']);
        $baseUrl = viaIsSet($this->params['base_url']);

        if ($sessionId) {
            CakeSession::id($sessionId);
            CakeSession::start();
        }
        if ($baseUrl) {
            Router::fullBaseUrl($baseUrl);
        }
        $this->components = new ComponentCollection();
        $this->AppController = new AppController();
        $this->NotifyBiz = new NotifyBizComponent($this->components);
        $this->components->disable('Security');
        $this->NotifyBiz->startup($this->AppController);
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
            'type'         => ['short' => 't', 'help' => '通知タイプ', 'required' => true,],
            'session_id'   => ['short' => 's', 'help' => 'セッションID', 'required' => true,],
            'base_url'     => ['short' => 'b', 'help' => 'ベースURL', 'required' => true,],
            'model_id'     => ['short' => 'm', 'help' => 'モデルID', 'required' => false,],
            'sub_model_id' => ['short' => 'n', 'help' => 'サブモデルID', 'required' => false,],
            'user_list'    => ['short' => 'u', 'help' => '送信先ユーザリスト', 'required' => false,],
            'user_id'      => ['short' => 'i', 'help' => '自分のユーザID', 'required' => true,],
            'team_id'      => ['short' => 'o', 'help' => 'チームID', 'required' => true,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        $to_user_list = null;
        if (isset($this->params['user_list'])) {
            $to_user_list = json_decode(base64_decode($this->params['user_list']), true);
        }
        $this->NotifyBiz->sendNotify($this->params['type'],
                                     isset($this->params['model_id']) ? $this->params['model_id'] : null,
                                     isset($this->params['sub_model_id']) ? $this->params['sub_model_id'] : null,
                                     $to_user_list,
                                     $this->params['user_id'],
                                     $this->params['team_id']
        );
    }

}
