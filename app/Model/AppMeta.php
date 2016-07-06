<?php
App::uses('AppModel', 'Model');

/**
 * AppMeta Model

 */
class AppMeta extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'key_name' => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'value'    => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'del_flg'  => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
}
