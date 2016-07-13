<?php
App::uses('AppController', 'Controller');

/**
 * Signup Controller
 *
 * @property Email $Email
 */
class SignupController extends AppController
{
    public $uses = [
        'Email',
    ];

    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = LAYOUT_ONE_COLUMN;
    }

    public function auth()
    {
    }

    public function user()
    {
    }

    public function password()
    {
    }

    public function team()
    {
    }

    public function term()
    {
    }

    /**
     * getting email verify code
     * [GET] method only
     * return value is json encoded
     * e.g.
     * {
     * error: false,//true or false,
     * message:"something",//if error is true then message exists. if no error, blank text
     * verify_code:"something",//string. if unnecessary, blank text
     * }
     * verify code can be returned in only the following cases.
     * 1. not exists
     * 2. not verified
     *
     * @param $email
     *
     * @return CakeResponse
     */
    public function ajax_get_email_verify_code($email)
    {
        $this->request->allowMethod('get');
        //init response values
        $res = [
            'error'       => false,
            'message'     => "",
            'verify_code' => "",
        ];

        try {
            //TODO WIP
            /** @noinspection PhpUndefinedMethodInspection */
            $code = $this->Email->getVerifyCode($email);
            $res['verify_code'] = $code;
        } catch (RuntimeException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }
        return $this->_ajaxGetResponse($res);
    }
}
