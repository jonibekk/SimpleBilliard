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
     * メールにてユーザ再認証
     *
     * @param $to_uid
     * @param $email_token
     *
     * @return null
     */
    public function sendMailEmailTokenResend($to_uid, $email_token)
    {
        $url = Router::url(
                     [
                         'admin'      => false,
                         'controller' => 'users',
                         'action'     => 'verify',
                         $email_token,
                     ], true);
        $this->SendMail->saveMailData($to_uid, SendMail::TYPE_TMPL_TOKEN_RESEND, ['url' => $url]);
        $this->execSendMailById($this->SendMail->id);
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
     * メールにてメールアドレス変更に伴う認証
     *
     * @param $to_uid
     * @param $email
     * @param $email_token
     *
     * @return null
     */
    public function sendMailChangeEmailVerify($to_uid, $email, $email_token)
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
        $this->SendMail->saveMailData($to_uid, SendMail::TYPE_TMPL_CHANGE_EMAIL_VERIFY,
                                      ['url' => $url, 'to' => $email]);
        $this->execSendMailById($this->SendMail->id);
    }

    /**
     * メールにてパスワード設定完了通知
     *
     * @param $to_uid
     *
     * @return null
     */
    public function sendMailCompletePasswordReset($to_uid)
    {
        $this->SendMail->saveMailData($to_uid, SendMail::TYPE_TMPL_PASSWORD_RESET_COMPLETE);
        $this->execSendMailById($this->SendMail->id);
    }

    /**
     * メールにてパスワード再設定
     *
     * @param $to_uid
     * @param $token
     *
     * @return null
     */
    public function sendMailPasswordReset($to_uid, $token)
    {
        $url = Router::url(
                     [
                         'admin'      => false,
                         'controller' => 'users',
                         'action'     => 'password_reset',
                         $token,
                     ], true);
        $this->SendMail->saveMailData($to_uid, SendMail::TYPE_TMPL_PASSWORD_RESET, ['url' => $url]);
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
