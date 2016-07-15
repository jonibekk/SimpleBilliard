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

    private $validations = [
        'User'      => [
            'first_name' => [
                'maxLength'      => ['rule' => ['maxLength', 128]],
                'notEmpty'       => ['rule' => 'notEmpty'],
                'isAlphabetOnly' => ['rule' => 'isAlphabetOnly'],
            ],
            'last_name'  => [
                'maxLength'      => ['rule' => ['maxLength', 128]],
                'notEmpty'       => ['rule' => 'notEmpty'],
                'isAlphabetOnly' => ['rule' => 'isAlphabetOnly'],
            ],
            'password'   => [
                'maxLength' => ['rule' => ['maxLength', 50]],
                'notEmpty'  => [
                    'rule' => 'notEmpty',
                ],
                'minLength' => [
                    'rule' => ['minLength', 8],
                ]
            ],
        ],
        'Email'     => [
            'email' => [
                'maxLength' => ['rule' => ['maxLength', 200]],
                'notEmpty'  => [
                    'rule' => 'notEmpty',
                ],
                'email'     => [
                    'rule' => ['email'],
                ],
            ],
        ],
        'LocalName' => [
            'first_name' => [
                'maxLength' => ['rule' => ['maxLength', 128]],
            ],
            'last_name'  => [
                'maxLength' => ['rule' => ['maxLength', 128]],
            ],
        ],
        'Team'      => [
            'start_term_month' => ['numeric' => ['rule' => ['numeric'],],],
            'border_months'    => ['numeric' => ['rule' => ['numeric'],],],
            'timezone'         => [
                'numeric' => [
                    'rule'       => ['numeric'],
                    'allowEmpty' => true,
                ],
            ],
        ]
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
            'error'          => false,
            'message'        => "",
            'validation_msg' => [],
        ];

        try {
            $data = $this->_filterWhiteList($this->request->data);
            if (empty($data)) {
                throw new RuntimeException(__('No Data'));
            }
            $validation_msg = $this->_getValidationErrorMsg($data);
            if (!empty($validation_msg)) {
                $res['validation_msg'] = $validation_msg;
                throw new RuntimeException(__('Invalid Data'));
            }
            //store session
            if($this->Session->read('data')){
                $data = array_merge($this->Session->read('data'), $data);
            }
            $this->Session->write(['data' => $data]);

        } catch (RuntimeException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }
        return $this->_ajaxGetResponse($res);
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function _getValidationErrorMsg($data)
    {
        $validation_msg = [];
        foreach ($data as $model => $fields) {
            /**
             * @var AppModel $Model
             */
            $Model = ClassRegistry::init($model);
            $Model->set($fields);
            $Model->validate = $this->validations[$model];
            if (!$Model->validates()) {
                $validation_msg[$model] = $Model->validationErrors;
            }
        }
        return $validation_msg;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function _filterWhiteList($data)
    {
        //filter Model
        $data = array_intersect_key($data, $this->validations);
        //filter fields
        foreach ($data as $model => $fields) {
            $data[$model] = array_intersect_key($data[$model], $this->validations[$model]);
            if (empty($data[$model])) {
                unset($data[$model]);
            }
        }
        return $data;
    }

}
