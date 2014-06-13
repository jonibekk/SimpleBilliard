<?php
App::uses('AppModel', 'Model');

/**
 * Email Model
 *
 * @property User $User
 */
class Email extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'        => ['uuid' => ['rule' => ['uuid']]],
        'email'          => [
            'notEmpty'      => [
                'rule' => 'notEmpty',
            ],
            'email'         => [
                'rule' => ['email'],
            ],
            'emailIsUnique' => [
                'rule' => ['isUnique'],
            ]
        ],
        'email_verified' => ['boolean' => ['rule' => ['boolean']]],
        'del_flg'        => ['boolean' => ['rule' => ['boolean']]],
    ];

    public $validate_password_exists = [
        'email' => [
            'isEmailExists' => [
                'rule' => ['isEmailExists'],
            ]
        ],
    ];

    /**
     * @param $data
     *
     * @return bool
     */
    public function validateEmailExists($data)
    {
        $this->set($data);
        $this->validate = $this->validate_password_exists;
        return $this->validates();
    }

    /**
     * メールアドレスの存在チェック
     *
     * @param $check
     *
     * @return bool
     */
    public function isEmailExists($check)
    {
        if (isset($check['email']) && empty($this->findByEmail($check['email']))) {
            return false;
        }
        return true;
    }

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
    ];
}
