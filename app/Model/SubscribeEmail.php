<?php
App::uses('AppModel', 'Model');

/**
 * SubscribeEmail Model
 */
class SubscribeEmail extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'email'   => [
            'notBlank'      => [
                'rule' => 'notBlank',
            ],
            'email'         => [
                'rule' => ['email'],
            ],
            'emailIsUnique' => [
                'rule' => ['isUnique'],
            ]
        ],
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
}
