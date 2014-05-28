<?php

/**
 * @author daikihirakata
 * @property LangComponent $Lang
 * @property User          $User
 * @property SendMail      $SendMail
 */
class GlEmailComponent extends Object
{

    public $name = "GlEmail";

    public $Controller;

    public $notifi_mails = array();

    public $User;

    public $SendMail;

    public $components = array(
        'Lang',
    );

    function initialize()
    {
    }

    function startup(&$controller)
    {
        $this->Controller = $controller;
        $this->User = ClassRegistry::init('User');
        $this->SendMail = ClassRegistry::init('SendMail');
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
     * メールにてユーザ認証
     *
     * @param $to_uid
     * @param $email_token
     *
     * @return null
     */
    public function sendMailUserVerify($to_uid, $email_token)
    {
        if (!$to_uid || !$email_token) {
            return null;
        }
        $url = Router::url(
                     [
                         'admin'      => false,
                         'controller' => 'users',
                         'action'     => 'verify',
                         $email_token,
                     ], true);
        $this->SendMail->saveMailData($to_uid, SendMail::TYPE_TMPL_ACCOUNT_VERIFY, ['url' => $url]);
        $this->execSendMailById($this->SendMail->id);
    }

    /**
     * execコマンドにてidを元にメール送信を行う
     *
     * @param $id
     */
    public function execSendMailById($id)
    {
        $set_web_env = "";
        $php = "/usr/bin/php ";
        $cake_cmd = $php . APP . "Console" . DS . "cake.php";
        $cake_app = " -app " . APP;
        $cmd = " send_mail send_mail_by_id";
        $cmd .= " -i " . $id;
        $cmd_end = " > /dev/null &";
        $all_cmd = $set_web_env . $cake_cmd . $cake_app . $cmd . $cmd_end;

        exec($all_cmd);
    }

}
