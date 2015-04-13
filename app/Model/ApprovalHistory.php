<?php
App::uses('AppModel', 'Model');

/**
 * ApprovalHistory Model
 *
 * @property Collaborator $Collaborator
 * @property User         $User
 */
class ApprovalHistory extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'action_status' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'       => [
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
        'Collaborator',
        'User',
    ];
}
