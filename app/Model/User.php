<?php
App::uses('AppModel', 'Model');

/**
 * User Model

 */
class User extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'password'   => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'email'      => [
            'email' => [
                'rule' => ['email'],
            ],
        ],
        'first_name' => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'last_name'  => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
    ];

    /**
     * @param $id
     *
     * @return array|null
     */
    function getUser($id)
    {
        if (!$id) {
            return null;
        }
        return $this->find('first', $id);
    }
}
