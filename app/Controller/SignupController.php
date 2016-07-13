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
        $allowed_actions = ['ajax_generate_email_verify_code'];
        //ajaxのPOSTではフォーム改ざんチェック用のハッシュ生成ができない為、ここで改ざんチェックを除外指定
        if (in_array($this->request->params['action'], $allowed_actions)) {
            $this->Security->validatePost = false;
        }

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
     * store 6digit code to session
     * [POST] method only allowed
     * required field is:
     * $this->request->data['email']
     * return value is json encoded
     * e.g.
     * {
     * error: false,//true or false,
     * message:"something is wrong",//if error is true then message exists. if no error, blank text
     * }
     * verify code can be sent in only not verified.
     * if not verified and record exists, remove and regenerate it.
     *
     * @return CakeResponse
     */
    public function ajax_generate_email_verify_code()
    {
        $this->_ajaxPreProcess();
        $this->request->allowMethod('post');
        //init response values
        $res = [
            'error'   => false,
            'message' => "",
        ];

        try {
            if (!isset($this->request->data['email'])) {
                throw new RuntimeException(__('Invalid fields'));
            }
            //TODO WIP
            /** @noinspection PhpUndefinedMethodInspection */
            $code = $this->Email->getVerifyCode($this->request->data['email']);
            $res['verify_code'] = $code;

            $this->Session->write('email_verify_code', $code);

        } catch (RuntimeException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }
        return $this->_ajaxGetResponse($res);
    }
}
