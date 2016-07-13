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
     * generating email verify code
     * and sending it by e-mail
     * [GET] method only
     * return value is json encoded
     * e.g.
     * {
     * error: false,//true or false,
     * message:"something is wrong",//if error is true then message exists. if no error, blank text
     * }
     * verify code can be sent in only not verified.
     * if not verified and record exists, remove and regenerate it.
     *
     * @param $email
     *
     * @return CakeResponse
     */
    public function ajax_generate_email_verify_code($email)
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

            //store 6digit code for session

        } catch (RuntimeException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }
        return $this->_ajaxGetResponse($res);
    }
}
