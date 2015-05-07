<?php
App::uses('AppModel', 'Model');

/**
 * SendMailToUser Model
 *
 * @property SendMail     $SendMail
 * @property User         $User
 * @property Team         $Team
 */
class SendMailToUser extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'SendMail',
        'User',
        'Team',
    ];

    function getToUserList($send_mail_id)
    {
        $options = [
            'conditions' => [
                'send_mail_id' => $send_mail_id,
            ],
            'fields'     => ['user_id']
        ];
        $res = $this->find('list', $options);
        return $res;
    }

}
