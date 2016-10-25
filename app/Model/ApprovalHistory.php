<?php
App::uses('AppModel', 'Model');

/**
 * ApprovalHistory Model
 *
 * @property GoalMember $GoalMember
 * @property User       $User
 */
class ApprovalHistory extends AppModel
{

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
        'action_status'           => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'select_clear_status'     => [
            'numeric' => [
                'rule' => ['range', -1, 3], // 0, 1, 2のみ許可
            ],
        ],
        'select_important_status' => [
            'numeric' => [
                'rule' => ['range', -1, 3], // 0, 1, 2のみ許可
            ],
        ],
        'comment'                 => [
            'isString'  => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
        ],
        'del_flg'                 => [
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
        'GoalMember',
        'User',
    ];

    /**
     * 認定ヒストリー保存
     *
     * @param array $saveData
     */
    function add($saveData)
    {
        $this->set($saveData['ApprovalHistory']);
        if (!$this->validates()) {
            return false;
        }

        return $this->save($saveData);
    }

    function findByGoalMemberId($goalMemberId)
    {
        $options = [
            'conditions' => [
                'goal_member_id' => $goalMemberId,
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * 認定IDからユーザー情報とセットで認定情報を取得する
     *
     * @param  $id
     *
     * @return array|null
     */
    function findByIdWithUser($id)
    {
        if (!$id) {
            return null;
        }

        $options = [
            'conditions' => [
                'ApprovalHistory.id' => $id,
            ],
            'fields' => [
                'ApprovalHistory.id',
                'ApprovalHistory.goal_member_id',
                'ApprovalHistory.user_id',
                'ApprovalHistory.comment',
                'ApprovalHistory.select_clear_status',
                'ApprovalHistory.select_important_status'
            ],
            'contain' => [
                'User'   => [
                    'fields' => $this->User->profileFields
                ]
            ]
        ];
        $res = $this->find('first', $options);
        if(!$res) {
            return null;
        }

        $res['ApprovalHistory']['User'] = Hash::get($res, 'User');
        unset($res['User']);
        return Hash::get($res, 'ApprovalHistory');
    }
}
