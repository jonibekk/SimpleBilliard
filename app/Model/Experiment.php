<?php
App::uses('AppModel', 'Model');

/**
 * Experiment Model
 */
class Experiment extends AppModel
{
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'    => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
}
