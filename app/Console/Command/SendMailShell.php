<?php
App::uses('Controller', 'Core');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');
App::uses('LangComponent', 'Controller/Component');

/**
 * SendMailShell
 *
 * @property Team                      $Team
 * @property User                      $User
 * @property SendMail                  $SendMail
 * @property LangComponent             $LangComponent
 */
class SendMailShell extends AppShell
{

    public $uses = array(
        'Team',
        'User',
        'SendMail'
    );
    public $components;
    public $Lang;
    public $Controller;
    public $AppController;
    /**
     * 言語別のメールデータ
     *
     * @var
     */
    public $lang_data;
    /**
     * @var
     */
    public $item;

    public $init_data;

    public function startup()
    {
        parent::startup();
        if ($this->params['session_id']) {
            CakeSession::destroy();
            CakeSession::id($this->params['session_id']);
            CakeSession::start();
        }
        $this->components = new ComponentCollection();
        $this->Lang = new LangComponent($this->components);
        $this->AppController = new AppController();
        $this->components->disable('Security');
    }

    public function __destruct()
    {
        unset($this->Lang);
        unset($this->components);
        unset($this->AppController);
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $commands = [
            'send_mail_by_id'        => [
                'help'   => 'SendMailのidを元にメールを送信する',
                'parser' => [
                    'options' => [
                        'id'         => ['short' => 'i', 'help' => 'SendMailのid', 'required' => true,],
                        'session_id' => ['short' => 's', 'help' => 'Session ID', 'required' => true,],
                    ]
                ]
            ],
            'send_notify_mail_by_id' => [
                'help'   => 'SendMailのidを元に通知メールを送信する',
                'parser' => [
                    'options' => [
                        'id'         => ['short' => 'i', 'help' => 'SendMailのid', 'required' => true,],
                        'session_id' => ['short' => 's', 'help' => 'Session ID', 'required' => true,],
                    ]
                ]
            ],
        ];
        $parser->addSubcommands($commands);
        return $parser;
    }

    public function main()
    {

    }

    /**
     * SendMailのidを元にメール送信を行う
     */
    public function send_mail_by_id()
    {
        try {
            $data = $this->_getDataAndProcessPreSend();
        } catch (RuntimeException $e) {
            return;
        }

        $this->item = json_decode($data['SendMail']['item'], true);
        $tmpl_type = $data['SendMail']['template_type'];
        $to_user_ids = $this->SendMail->SendMailToUser->getToUserList($data['SendMail']['id']);
        foreach ($to_user_ids as $to_user_id) {
            $data = $this->_getLangToUserData($to_user_id);
            $options = array_merge(SendMail::$TYPE_TMPL[$tmpl_type],
                                   ['to' => (isset($data['ToUser']['PrimaryEmail']['email'])) ? $data['ToUser']['PrimaryEmail']['email'] : null]
            );
            //送信先メールアドレスが指定されていた場合
            if (isset($this->item['to'])) {
                $options['to'] = $this->item['to'];
            }
            $viewVars = [
                'to_user_name'   => isset($data['ToUser']['display_username']) ? $data['ToUser']['display_username'] : null,
                'from_user_name' => (isset($data['FromUser']['display_username'])) ? $data['FromUser']['display_username'] : null,
            ];
            if (is_array($this->item)) {
                $viewVars = array_merge($this->item, $viewVars);
            }
            $this->_sendMailItem($options, $viewVars);
        }
        $this->SendMail->id = $data['SendMail']['id'];
        $this->SendMail->save(['sent_datetime' => time()]);
    }

    /**
     * SendMailのidを元に通知メール送信を行う
     */
    public function send_notify_mail_by_id()
    {
        try {
            $data = $this->_getDataAndProcessPreSend();
        } catch (RuntimeException $e) {
            return;
        }
        $this->item = json_decode($data['SendMail']['item'], true);
        $to_user_ids = $this->SendMail->SendMailToUser->getToUserList($data['SendMail']['id']);

        $notify_option = Notification::$TYPE[$data['Notification']['type']];

        foreach ($to_user_ids as $to_user_id) {
            $data = $this->_getLangToUserData($to_user_id, true);
            $from_user_names = [];
            foreach ($data['NotifyFromUser'] as $user) {
                $from_user_names[] = $user['User']['display_username'];
            }
            $subject = $this->User->Notification->getTitle($data['Notification']['type'],
                                                           $from_user_names,
                                                           $data['Notification']['count_num'],
                                                           $data['Notification']['item_name']
            );

            $options = [
                'to'       => $data['ToUser']['PrimaryEmail']['email'],
                'subject'  => $subject,
                'template' => $notify_option['mail_template'],
                'layout'   => 'default',
            ];
            $viewVars = [
                'to_user_name'   => $data['ToUser']['display_username'],
                'from_user_name' => $data['FromUser']['display_username'],
                'url'            => $this->item['url'],
                'body_title'     => $subject,
                'body'           => json_decode($data['Notification']['item_name'], true),
            ];
            $this->_sendMailItem($options, $viewVars);

        }
        $this->SendMail->id = $data['SendMail']['id'];
        $this->SendMail->save(['sent_datetime' => time()]);
    }

    private function _getDataAndProcessPreSend()
    {
        if (!$this->params['id']) {
            throw new RuntimeException();
        }
        //送信データを取得
        $data = $this->SendMail->getDetail($this->params['id']);
        if (!isset($data['SendMail'])) {
            throw new RuntimeException();
        }
        $this->init_data = $data;
        return $data;
    }

    function _getLangToUserData($to_user_id, $with_notify_from_user = false)
    {
        $data = $this->init_data;
        //ユーザデータを取得
        $to_user = $this->User->getProfileAndEmail($to_user_id);
        //言語設定
        //相手が存在するユーザなら相手の言語を採用
        if (isset($to_user['User']['language'])) {
            $lang = $to_user['User']['language'];
        }
        //相手が存在せず送信元のユーザが存在する場合は送信元ユーザの言語を採用
        elseif (isset($data['FromUser']['language'])) {
            $lang = $data['FromUser']['language'];
        }
        elseif (isset($this->item['language'])) {
            $lang = $this->item['language'];
        }
        //それ以外は英語
        else {
            $lang = "eng";
        }
        Configure::write('Config.language', $lang);
        //送信データを再取得
        if (isset($this->lang_data[$lang])) {
            $data = $this->lang_data[$lang];
        }
        else {
            $data = $this->SendMail->getDetail($this->params['id'], $lang, $with_notify_from_user);
            $this->lang_data[$lang] = $data;
        }
        //ToUserデータを付加
        $to_user = $this->User->getProfileAndEmail($to_user_id, $lang);
        $data['ToUser'] = $to_user['User'];
        return $data;
    }

    /**
     * @param array $options
     * @param array $viewVars
     *
     * @internal param \unknown $val
     */
    private function _sendMailItem($options, $viewVars)
    {
        $defaults = array(
            'subject'  => '',
            'template' => '',
            'to'       => '',
            'layout'   => 'default'
        );
        $options = array_merge($defaults, $options);
        $options['subject'] = "[" . SERVICE_NAME . "]" . $options['subject'];

        /**
         * @var CakeEmail $Email
         */
        $Email = $this->_getMailInstance();
        /** @noinspection PhpUndefinedMethodInspection */
        $Email->to($options['to'])->subject($options['subject'])
              ->template($options['template'], $options['layout'])->viewVars($viewVars)->send();
        $Email->reset();
    }

    /**
     * Returns a CakeEmail object
     *
     * @return object CakeEmail instance
     * @link http://book.cakephp.org/2.0/en/core-utility-libraries/email.html
     */
    protected function _getMailInstance()
    {
        App::uses('CakeEmail', 'Network/Email');
        if (ENV_NAME === "local") {
            return new CakeEmail('default');
        }
        else {
            return new CakeEmail('amazon');
        }
    }

}
