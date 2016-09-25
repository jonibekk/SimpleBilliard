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

    const ACTION_STATUS_NO_ACTION = 0;
    const ACTION_STATUS_ONLY_COMMENT = 1;
    const ACTION_STATUS_EVALUABLE = 2;
    const ACTION_STATUS_NOT_EVALUABLE = 3;
    const ACTION_STATUS_REQUEST_MODIFY = 4;
    const STATUS_IS_CLEAR_NO_SELECT = 0;
    const STATUS_IS_CLEAR = 1;
    const STATUS_IS_NOT_CLEAR = 2;
    const STATUS_IS_IMPORTANT_NO_SELECT = 0;
    const STATUS_IS_IMPORTANT = 1;
    const STATUS_IS_NOT_IMPORTANT = 2;

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
        'comment'       => [
            'isString'  => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
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

    function add($collaborator_id, $user_id, $action_status = 0, $comment = '')
    {
        if ($action_status === self::ACTION_STATUS_NO_ACTION && empty($comment) === true) {
            return false;
        }

        $param = [
            'collaborator_id' => $collaborator_id,
            'user_id'         => $user_id,
            'action_status'   => $action_status,
            'comment'         => $comment,
        ];

        return $this->save($param);
    }


    function findByCollaboratorId($collaboratorId)
    {
        $options = [
            'conditions' => [
                'collaborator_id' => $collaboratorId,
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /*
    function getHistory ($collaborator_id) {
        $options = [
            'conditions' => [
                'collaborator_id' => $collaborator_id,
            ],
            'fields'     => [
                'id', 'user_id', 'comment', 'created'
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }
    */
}
