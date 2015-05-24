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

    function add($collaborator_id, $user_id, $action_status = 0, $comment = '')
    {
        if ($action_status === self::ACTION_STATUS_NO_ACTION && empty($comment) === true) {
            return;
        }

        $param = [
            'collaborator_id' => $collaborator_id,
            'user_id'         => $user_id,
            'action_status'   => $action_status,
            'comment'         => $comment,
        ];

        return $this->save($param);
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
