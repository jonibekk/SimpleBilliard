<?php
App::uses('AppModel', 'Model');

/**
 * CreditCard Model
 *
 */
class CreditCard extends AppModel {

    public $validate = [
        'team_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
            'isUnique'  => [
                'rule' => ['isUnique', ['team_id', 'team_id'], false],
                'required' => 'create'
            ],
        ],
        'payment_setting_id' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'customer_code' => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'isString'  => ['rule' => 'isString', 'message' => 'Invalid Submission'],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
    ];
}
