<?php
App::uses('AppController', 'Controller');
App::uses('AppUtil', 'Util');

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
            'first_name'       => [
                'maxLength'    => ['rule' => ['maxLength', 128]],
                'notBlank'     => ['rule' => 'notBlank'],
                'userNameChar' => ['rule' => ['userNameChar']],
            ],
            'last_name'        => [
                'maxLength'    => ['rule' => ['maxLength', 128]],
                'notBlank'     => ['rule' => 'notBlank'],
                'userNameChar' => ['rule' => ['userNameChar']],
            ],
            'password'         => [
                'maxLength'      => ['rule' => ['maxLength', 50]],
                'notBlank'       => [
                    'rule' => 'notBlank',
                ],
                'minLength'      => [
                    'rule' => ['minLength', 8],
                ],
                'passwordPolicy' => [
                    'rule' => [
                        'custom',
                        '/^(?=.*[0-9])(?=.*[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{0,}$/i',
                    ]
                ]
            ],
            'local_date'       => [
                'notBlank' => ['rule' => 'notBlank',],
            ],
            'birth_day'        => [
                // TODO:アプリ申請のため、一時的に誕生日のバリデーションを無効にする
                //'rule'     => ['date', 'ymd'],
                //'notBlank' => ['rule' => 'notBlank'],
            ],
            'update_email_flg' => [
                'boolean' => [
                    'rule' => ['boolean',]
                ],
            ],
        ],
        'Email'     => [
            'email' => [
                'maxLength' => ['rule' => ['maxLength', 200]],
                'notBlank'  => [
                    'rule' => 'notBlank',
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
            'name'          => [
                'isString'  => [
                    'rule' => ['isString',],
                ],
                'maxLength' => ['rule' => ['maxLength', 128]],
                'notBlank'  => ['rule' => ['notBlank'],],
            ],
            'border_months' => ['numeric' => ['rule' => ['numeric'],],],
            'timezone'      => [
                'numeric' => [
                    'rule'       => ['numeric'],
                    'allowEmpty' => true,
                ],
            ],
        ],
        'Term'      => [
            'next_start_ym' => [
                'notBlank'     => [
                    'required' => 'update',
                    'rule'     => 'notBlank',
                ],
                'dateYm'       => [
                    'rule' => ['date', 'ym'],
                ],
                'startEndDate' => [
                    'rule' => ['customValidNextStartDateInSignup']
                ],
            ]
        ]
    ];

    private $requiredFields = [
        'User'  => [
            'first_name',
            'last_name',
            'password',
            'local_date',
            'birth_day',
            'update_email_flg'
        ],
        'Email' => [
            'email'
        ],
        'Team'  => [
            'name',
            'border_months',
            'timezone',
        ],
        'Term'  => [
            'next_start_ym'
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

        if ($this->_isBasicAuthRequiredOnSignup()) {
            $this->_setBasicAuth();
        }

        $this->layout = LAYOUT_ONE_COLUMN;
        //ajaxのPOSTではフォーム改ざんチェック用のハッシュ生成ができない為、ここで改ざんチェックを除外指定
        $this->Security->validatePost = false;
        //すべてのアクションは認証済みである必要がない
        $this->Auth->allow();
        //ログインしている場合はこのコントローラの全てのアクションにアクセスできない。
        if ($this->Auth->user()) {
            $this->Notification->outError(__('Invalid screen transition.'));
            $this->redirect('/');
        }
    }

    /**
     * @return bool
     */
    function _isBasicAuthRequiredOnSignup(): bool
    {
        if ($this->is_mb_app) {
            return false;
        }

        // target env
        if (!in_array(ENV_NAME, ['isao', 'stg'])) {
            return false;
        }

        // target actions
        if (!in_array($this->request->params['action'], ['email'])) {
            return false;
        }

        return true;
    }

    public function email()
    {
        if (!$this->request->is('post')) {
            return $this->render();
        }
        try {
            if (!isset($this->request->data['Email']['email'])) {
                throw new RuntimeException(__('Invalid fields'));
            }
            $email = $this->request->data['Email']['email'];
            $this->_emailValidate($email);
            $code = $this->Email->generateToken(6, '123456789');
            $formatted_code = number_format($code, 0, '.', '-');
            GoalousLog::Error($code);
            $this->Session->write('email_verify_code', $code);
            $this->Session->write('email_verify_start_time', REQUEST_TIMESTAMP);
            $this->Session->write('data.Email.email', $email);
            //send mail
            $this->GlEmail->sendEmailVerifyDigit($formatted_code, $email);
            return $this->redirect(['action' => 'auth']);
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            return $this->redirect($this->referer());
        }
        return $this->render();
    }

    public function auth()
    {
        $timezones = AppUtil::getTimezoneList();
        $signup_inputed_email = $this->Session->read('data.Email.email');
        $this->set(compact('timezones', 'signup_inputed_email'));
        $this->render('index');
    }

    /**
     * メールアドレスが登録可能なものか確認
     *
     * @return CakeResponse
     */
    public function ajax_validate_email()
    {
        $this->_ajaxPreProcess();
        $email = $this->request->query('email');
        $valid = true;
        $message = '';
        try {
            if (!$email) {
                throw new RuntimeException(__('Invalid fields'));
            }
            $this->_emailValidate($email);
        } catch (RuntimeException $e) {
            $message = $e->getMessage();
            $valid = false;
        }
        return $this->_ajaxGetResponse([
            'valid'   => $valid,
            'message' => $message
        ]);
    }

    function _emailValidate($email)
    {
        // メールアドレスだけ validate
        $this->User->Email->create(['email' => $email]);
        $this->Email->validate = [
            'email' => [
                'maxLength' => ['rule' => ['maxLength', 255]],
                'notBlank'  => ['rule' => 'notBlank',],
                'email'     => ['rule' => ['email'],],
            ],
        ];
        if (!$this->Email->validates(['fieldList' => ['email']])) {
            throw new RuntimeException($this->Email->concatValidationErrorMsg());
        }
        if ($this->Email->isVerified($email)) {
            throw new RuntimeException(__('This email address has already been used. Use another email address.'));
        }
        return true;
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
            GoalousLog::error(serialize($this->request->data));
            GoalousLog::error(serialize($data));
            if (empty($data)) {
                throw new RuntimeException(__('No Data'));
            }
            $validation_msg = $this->_getValidationErrorMsg($data, true);
            if (!empty($validation_msg)) {
                $res['validation_msg'] = $validation_msg;
                throw new RuntimeException(__('Invalid Data'));
            }

            $birthdayArray = explode('-', $data['User']['birth_day']);
            $birthday = array(
                'year' => $birthdayArray[0], 
                'month' => $birthdayArray[1], 
                'day' => $birthdayArray[2] 
            );

            if (!$this->checkAge(16, $birthday, $data['User']['local_date']))
            {
                $validation_msg['data[User][age]'] = __('You must be at least 16 years old to register Goalous.'); 
                $res['validation_msg'] = $validation_msg;
                throw new RuntimeException(__('Invalid Data'));
            }
            
            //store session
            if ($this->Session->read('data')) {
                $data = Hash::merge($this->Session->read('data'), $data);
            }

            $this->Session->write(['data' => $data]);

        } catch (RuntimeException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }
        return $this->_ajaxGetResponse($res);
    }

    /**
     * register user
     * POST method only
     * input fields are the following
     * $this->request->data['Team']['start_term_month']
     * $this->request->data['Team']['border_months']
     * $this->request->data['Team']['timezone']
     *
     * @return CakeResponse|null
     */
    public function ajax_register_user()
    {
        $this->_ajaxPreProcess();
        $this->request->allowMethod('post');
        //init response values
        $res = [
            'error'            => false,
            'message'          => "",
            'validation_msg'   => [],
            'is_not_available' => false,
        ];

        $requestData = $this->request->data;
        $sessionData = $this->Session->read('data');

        try {
            $this->User->begin();
            if (!$sessionData) {
                throw new RuntimeException(__('Invalid screen transition.'));
            }

            $data = $this->_filterWhiteList($requestData);

            if (empty($data)) {
                throw new RuntimeException(__('No Data'));
            }

            $validation_msg = $this->_getValidationErrorMsg($data, true);
            if (!empty($validation_msg)) {
                $res['validation_msg'] = $validation_msg;
                throw new RuntimeException(__('Invalid Data'));
            }
            //merge form data and session data
            $data = Hash::merge($sessionData, $data);
            //required fields check
            if (!$this->_hasAllRequiredFields($data)) {
                $res['is_not_available'] = true;
                throw new RuntimeException(__('Some error occurred. Please try again from the start.'));
            }
            //if already verified email, display error
            if (isset($data['Email']['email']) && $this->Email->isVerified($data['Email']['email'])) {
                throw new RuntimeException(__('This email address has already been used. Use another email address.'));
            }
            //validation for all datas
            $validation_msg = $this->_getValidationErrorMsg($data, true);
            if (!empty($validation_msg)) {
                $res['is_not_available'] = true;
                throw new RuntimeException(__('Some error occurred. Please try again from the start.'));
            }
            //preparing data before saving
            $data['User']['language'] = $this->Lang->getLanguage();
            $data['User']['timezone'] = AppUtil::getClientTimezone($data['User']['local_date']);
            unset($data['User']['local_date']);
            if (isset($data['LocalName'])) {
                $data['LocalName']['language'] = $this->Lang->getLanguage();
            }

            //saving user datas
            $this->User->userRegistrationNewForm($data);
            $user_id = $this->User->getLastInsertID() ? $this->User->getLastInsertID() : $this->User->id;

            ///save team
            $this->Team->add(['Team' => $data['Team']], $user_id);
            $teamId = $this->Team->getLastInsertID();

            // save current&next term
            $Term = ClassRegistry::init('Term');
            $nextStartDate = date('Y-m-01', strtotime($data['Term']['next_start_ym']));
            $termRange = $data['Team']['border_months'];
            $currentStartDate = date('Y-m-01');
            if (!$this->Team->Term->createInitialDataAsSignup($currentStartDate, $nextStartDate, $termRange, $teamId)) {
                $res['is_not_available'] = true;
                throw new RuntimeException(__('Some error occurred. Please try again from the start.'));
            }

            //success!!
            //auto login with team
            $this->_autoLogin($user_id);

            // Change mode to open modal on top page
            $this->Session->write('add_new_mode', MODE_NEW_PROFILE);

            //after success
            $this->Session->delete('data');

        } catch (RuntimeException $e) {
            CakeLog::error(sprintf("Failed to signup. msg: %s, requestData: %s",
                    $e->getMessage(),
                    AppUtil::jsonOneLine($requestData)
            ));
            $res['error'] = true;
            $res['message'] = $e->getMessage();
            $this->User->rollback();
        }
        $this->User->commit();

        return $this->_ajaxGetResponse($res);
    }

    /**
     * @param      $data
     * @param bool $reformat
     *
     * @return array
     */
    public function _getValidationErrorMsg($data, $reformat = false)
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
        if ($reformat) {
            $formatted_validation_msg = [];
            foreach ($validation_msg as $model => $fields) {
                foreach ($fields as $field => $msg) {
                    $formatted_validation_msg["data[$model][$field]"] = $msg[0];
                }
            }
            return $formatted_validation_msg;
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

    public function _hasAllRequiredFields($data)
    {
        foreach ($this->requiredFields as $model => $fields) {
            foreach ($fields as $field) {
                if (!isset($data[$model][$field])) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * check Age
     * 
     */
    private function checkAge(int $age, array $birthday, string $localDate): bool
    {
        $year = $birthday['year'];
        $month = $birthday['month'];
        $day = $birthday['day'];
        if (empty($year) || empty($month) || empty($day)){
            return true;
        }
        $birthDate = GoalousDateTime::createFromFormat("Ymd", $year.$month.$day)->startOfDay();
        $userLocalDate = GoalousDateTime::parse($localDate)->startOfDay();
        $age = $userLocalDate->diffInYears($birthDate);
        if ($age < 16) {
            return false;
        }
        return true;

    }

}
