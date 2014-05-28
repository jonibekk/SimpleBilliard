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

    public function startup()
    {
        parent::startup();
        $this->components = new ComponentCollection();
        $this->Lang = $this->components->load('Lang');
        $this->AppController = new AppController();
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
            'send_mail_by_id' => [
                'help'   => 'SendMailのidを元にメールを送信する',
                'parser' => [
                    'options' => [
                        'id' => ['short' => 'i', 'help' => 'SendMailのid', 'required' => true,],
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
        if (!$this->params['id']) {
            return;
        }
        //送信データを取得
        $data = $this->SendMail->getDetail($this->params['id']);
        if (!isset($data['SendMail'])) {
            return;
        }
        $item = json_decode($data['SendMail']['item'], true);
        $tmpl_type = $data['SendMail']['template_type'];
        $options = array_merge(SendMail::$TYPE_TMPL[$tmpl_type],
                               ['to' => (isset($data['ToUser']['PrimaryEmail']['email'])) ? $data['ToUser']['PrimaryEmail']['email'] : null]
        );

        //言語設定
        Configure::write('Config.language', $data['ToUser']['language']);
        $viewVars = [
            'to_user_name'   => $data['ToUser']['display_username'],
            'from_user_name' => (isset($data['FromUser']['display_username'])) ? $data['FromUser']['display_username'] : null,
            'url'            => (isset($item['url'])) ? $item['url'] : null,
        ];
        $this->_sendMailItem($options, $viewVars);
        $this->SendMail->id = $data['SendMail']['id'];
        $this->SendMail->save(['sent_datetime' => date('Y-m-d H:i:s')]);
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

        $Email = $this->_getMailInstance();
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
