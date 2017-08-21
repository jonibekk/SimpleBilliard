<?php
App::uses('AppModel', 'Model');
/**
 * Invoice Model
 *
 */
class Invoice extends AppModel {

    /* Validation rules
    *
    * @var array
    */
    public $validate = [
        'team_id'          => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_name'     => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'isString'  => ['rule' => 'isString'],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_post_code'       => [
            'maxLength'    => ['rule' => ['maxLength', 16]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_region'       => [
            'maxLength'    => ['rule' => ['maxLength', 255]],
            'notBlank'  => [
                'rcacequired' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_city'       => [
            'maxLength'    => ['rule' => ['maxLength', 255]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_street'       => [
            'maxLength'    => ['rule' => ['maxLength', 255]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_tel'          => [
            'maxLength' => ['rule' => ['maxLength', 20]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
            'phoneNo'   => [
                'rule' => 'phoneNo',
            ],
        ],
        'contact_person_first_name'         => [
            'maxLength'    => ['rule' => ['maxLength', 128]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'contact_person_first_name_kana'         => [
            'maxLength'    => ['rule' => ['maxLength', 128]],
            'notBlank'     => ['rule' => 'notBlank'],
        ],
        'contact_person_last_name'         => [
            'maxLength'    => ['rule' => ['maxLength', 128]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'contact_person_last_name_kana'         => [
            'maxLength'    => ['rule' => ['maxLength', 128]],
            'notBlank'     => ['rule' => 'notBlank'],
        ],
        'contact_person_tel'       => [
            'maxLength'    => ['rule' => ['maxLength', 20]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'contact_person_email' => [
            'notBlank'    => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
            'emailsCheck' => [
                'rule' => ['emailsCheck']
            ],
        ],
    ];
}
