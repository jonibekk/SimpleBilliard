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
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
}