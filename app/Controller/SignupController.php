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
        //ajaxのPOSTではフォーム改ざんチェック用のハッシュ生成ができない為、ここで改ざんチェックを除外指定
        $this->Security->validatePost = false;
        //すべてのアクションは認証済みである必要がない
        $this->Auth->allow();
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
     * store status to redis
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
            $this->Email->validate = [
                'email' => [
                    'maxLength' => ['rule' => ['maxLength', 200]],
                    'notEmpty'  => ['rule' => 'notEmpty',],
                    'email'     => ['rule' => ['email'],],
                ],
            ];
            $this->Email->set($this->request->data);
            if (!$this->Email->validates()) {
                throw new RuntimeException($this->Email->concatValidationErrorMsg());
            }
            if ($this->Email->isVerified($this->request->data['email'])) {
                throw new RuntimeException(__('This email address has already been used. Use another email address.'));
            }
            $code = $this->Email->generateToken(6, '123456789');
            $formatted_code = number_format($code, 0, '.', '-');
            $this->Session->write('email_verify_code', $code);
            $this->Session->write('email_verify_start_time', REQUEST_TIMESTAMP);
            $this->Session->write('data.Email.email', $this->request->data['email']);
            //send mail
            $this->GlEmail->sendEmailVerifyDigit($formatted_code, $this->request->data['email']);
        } catch (RuntimeException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }
        return $this->_ajaxGetResponse($res);
    }

    /**
     * verify email by verify code
     * [POST] method only allowed
     * required field is:
     * $this->request->data['code']
     * return value is json encoded
     * e.g.
     * {
     * error: false,//true or false,
     * message:"something is wrong",//if error is true then message exists. if no error, blank text
     * is_locked: false,//true or false,
     * is_expired: false,//true or false,
     * }
     * TTL is 1 hour
     * 5 failed then lockout 5mins
     * compare input field and session stored
     * DB is not updated, it will be updated in final user registration part.
     *
     * @return CakeResponse
     */
    public function ajax_verify_code()
    {
        $this->_ajaxPreProcess();
        $this->request->allowMethod('post');
        //init response values
        $res = [
            'error'      => false,
            'message'    => "",
            'is_locked'  => false,
            'is_expired' => false,
        ];

        try {
            //required session variables
            if (!$this->Session->read('email_verify_start_time') ||
                !$this->Session->read('email_verify_code') ||
                !$this->Session->read('data.Email.email')
            ) {
                throw new RuntimeException(__('Invalid screen transition'));
            }
            if (!isset($this->request->data['code'])) {
                throw new RuntimeException(__('Param is incorrect'));
            }
            //it can be verified within 1 hour from start verification.
            if ($this->Session->read('email_verify_start_time') < REQUEST_TIMESTAMP - 60 * 60) {
                $res['is_expired'] = true;
                throw new RuntimeException(__('Code Verification has expired'));
            }
            //comparing input code and stored code
            if ($this->request->data['code'] != $this->Session->read('email_verify_code')) {
                $is_locked = $this->GlRedis->isEmailVerifyCodeLocked(
                    $this->Session->read('data.Email.email'),
                    $this->request->clientIp()
                );
                if ($is_locked) {
                    $res['is_locked'] = true;
                }
                throw new RuntimeException(__('verification code was wrong'));
            }

            //success!
            $this->Session->delete('email_verify_code');
            $this->Session->delete('email_verify_start_time');

        } catch (RuntimeException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }
        return $this->_ajaxGetResponse($res);
    }


    /**
     * validation fields
     * e.g.
     * $this->request->data['User']['first_name']
     *
     * @return CakeResponse|null
     */
    public function ajax_validation_fields()
    {
        $this->_ajaxPreProcess();
        $this->request->allowMethod('post');
        //init response values
        $res = [
            'error'   => false,
            'message' => "",
        ];
        //TODO WIP
        $white_list = [
            'User'  => [
                'first_name',
                'last_name'
            ],
            'Email' => [
                'email'
            ]
        ];
        try {
            if (empty($this->request->data)) {
                throw new RuntimeException(__('No Datas'));
            }
            //white list checking

        } catch (RuntimeException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }
        return $this->_ajaxGetResponse($res);
    }
}
