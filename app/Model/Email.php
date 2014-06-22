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
        'user_id'        => [
            'uuid' => [
                'rule' => 'uuid'
            ],
        ],
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

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
    ];

    public function isAllVerified($uid)
    {
        $options = [
            'conditions' => [
                'user_id'        => $uid,
                'email_verified' => false,
            ]
        ];
        $res = $this->find('first', $options);
        if (empty($res)) {
            return true;
        }
        return false;
    }

    /**
     * まだ認証が終わっていないメールを返却
     *
     * @param $uid
     *
     * @return array
     */
    public function getNotVerifiedEmail($uid)
    {
        $options = [
            'conditions' => [
                'user_id'        => $uid,
                'email_verified' => false,
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }
}
