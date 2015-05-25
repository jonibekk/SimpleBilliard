<?php
App::uses('AppModel', 'Model');

/**
 * Collaborator Model
 *
 * @property Team      $Team
 * @property Goal      $Goal
 * @property User      $User
 */
class Collaborator extends AppModel
{
    /**
     * タイプ
     */
    const TYPE_COLLABORATOR = 0;
    const TYPE_OWNER = 1;

    static public $TYPE = [
        self::TYPE_COLLABORATOR => "",
        self::TYPE_OWNER        => "",
    ];

    const STATUS_UNAPPROVED = 0;
    const STATUS_APPROVAL = 1;
    const STATUS_HOLD = 2;
    const STATUS_MODIFY = 3;

    /**
     * タイプの表示名をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[self::TYPE_COLLABORATOR] = __d('gl', "コラボレータ");
        self::$TYPE[self::TYPE_OWNER] = __d('gl', "オーナ");
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'type'    => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg' => [
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
        'Team',
        'Goal',
        'GoalCategory',
        'User',
    ];

    public $hasMany = [
        'ApprovalHistory',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTypeName();
    }

    function add($goal_id, $uid = null, $type = self::TYPE_COLLABORATOR)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $collaborator = [
            'team_id' => $this->current_team_id,
            'user_id' => $uid,
            'type'    => $type,
            'goal_id' => $goal_id,
        ];
        $res = $this->save($collaborator);
        return $res;
    }

    function edit($data, $uid = null, $type = self::TYPE_COLLABORATOR)
    {
        if (!isset($data['Collaborator']) || empty($data['Collaborator'])) {
            return false;
        }
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $data['Collaborator']['user_id'] = $uid;
        $data['Collaborator']['team_id'] = $this->current_team_id;
        $data['Collaborator']['type'] = $type;

        $res = $this->save($data);
        $this->Goal->Follower->deleteFollower($data['Collaborator']['goal_id']);
        return $res;
    }

    function getCollaboGoalList($user_id, $with_owner = false, $limit = null, $page = 1, $approval_status = null)
    {
        $options = [
            'conditions' => [
                'user_id' => $user_id,
                'team_id' => $this->current_team_id,
                'type'    => [
                    Collaborator::TYPE_COLLABORATOR,
                ],
            ],
            'fields'     => [
                'goal_id', 'goal_id'
            ],
            'page'       => $page,
            'limit'      => $limit
        ];
        if ($with_owner) {
            unset($options['conditions']['type']);
        }
        if ($approval_status) {
            $options['conditions']['valued_flg'] = $approval_status;
        }
        $res = $this->find('list', $options);
        return $res;
    }

    function isCollaborated($goal_id, $uid = null)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $options = [
            'conditions' => [
                'Collaborator.goal_id' => $goal_id,
                'Collaborator.user_id' => $uid,
            ],
        ];
        $res = $this->find('first', $options);
        if (!empty($res)) {
            return true;
        }
        return false;
    }

    function getCollaboGoalDetail($team_id, $goal_user_id, $approval_flg, $is_include_priority_0 = true)
    {
        $options = [
            'fields'     => ['id', 'type', 'role', 'priority', 'valued_flg'],
            'conditions' => [
                'Collaborator.team_id'    => $team_id,
                'Collaborator.user_id'    => $goal_user_id,
                'Collaborator.valued_flg' => $approval_flg,
            ],
            'contain'    => [
                'Goal'            => [
                    'fields'       => [
                        'name', 'goal_category_id', 'end_date', 'photo_file_name',
                        'value_unit', 'target_value', 'start_value', 'description'
                    ],
                    'Purpose'      => ['fields' => 'name'],
                    'GoalCategory' => ['fields' => 'name'],
                ],
                'User'            => [
                    'fields' => $this->User->profileFields
                ],
                'ApprovalHistory' => [
                    'User'   => [
                        'fields' => $this->User->profileFields
                    ],
                    'fields' => ['user_id', 'comment', 'created'],
                    'order'  => ['ApprovalHistory.created DESC'],
                    //'limit' => 1,
                ]
            ],
            'type'       => 'inner',
            'order'      => ['Collaborator.created DESC'],
        ];
        if (!$is_include_priority_0) {
            $options['conditions']['NOT'] = array('Collaborator.priority' => "0");
        }
        if (is_array($approval_flg)) {
            unset($options['conditions']['Collaborator.valued_flg']);
            foreach ($approval_flg as $val) {
                $options['conditions']['OR'][]['Collaborator.valued_flg'] = $val;
            }
        }
        $res = $this->find('all', $options);
        return $res;
    }

    function changeApprovalStatus($id, $status)
    {
        $this->id = $id;
        $this->save(['valued_flg' => $status]);
    }

    function countCollaboGoal($team_id, $user_id, $goal_user_id, $approval_flg)
    {
        $options = [
            'fields'     => ['id', 'type', 'valued_flg', 'priority'],
            'conditions' => [
                'Collaborator.team_id'    => $team_id,
                'Collaborator.user_id'    => $goal_user_id,
                'Collaborator.valued_flg' => $approval_flg,
            ],
            'contain'    => [
                'Goal' => [
                    'fields'       => ['id'],
                    'Purpose'      => ['fields' => 'id'],
                    'GoalCategory' => ['fields' => 'id'],
                ],
                'User' => [
                    'fields' => ['id'],
                ],
            ],
            'type'       => 'inner',
        ];

        $res = [];
        foreach ($this->find('all', $options) as $key => $val) {
            if ($this->Goal->isPresentTermGoal($val['Goal']['id']) === false) {
                continue;
            }
            // 自分のゴール + 修正待ち以外
            if ($val['User']['id'] === (string)$user_id && $val['Collaborator']['valued_flg'] !== '3') {
                continue;
            }
            // 自分のゴール + 修正待ち + コラボレーター
            if ($val['User']['id'] === (string)$user_id && $val['Collaborator']['valued_flg'] === '3'
                && $val['Collaborator']['type'] === '0'
            ) {
                continue;
            }
            //他人のゴール + 重要度0 = 対象外
            if ($val['User']['id'] !== (string)$user_id && $val['Collaborator']['priority'] === '0') {
                continue;
            }
            $res[] = $val;
        }
        return count($res);
    }

    function getLeaderUid($goal_id)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_id,
                'team_id' => $this->current_team_id,
                'type'    => [
                    Collaborator::TYPE_OWNER,
                ],
            ],
            'fields'     => [
                'user_id'
            ],
        ];
        $res = $this->find('first', $options);
        if (viaIsSet($res['Collaborator']['user_id'])) {
            return $res['Collaborator']['user_id'];
        }
        return null;
    }

    /**
     * @param      $goal_id
     * @param null $type
     *
     * @return array
     */
    function getCollaboratorListByGoalId($goal_id, $type = null)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_id,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'user_id', 'user_id'
            ],
        ];
        if ($type !== null) {
            $options['conditions']['type'] = $type;
        }
        $res = $this->find('list', $options);
        return $res;
    }

    function getCollaborator($team_id, $user_id, $goal_id, $owner = true)
    {
        $options = [
            'conditions' => [
                'team_id' => $team_id,
                'user_id' => $user_id,
                'goal_id' => $goal_id,
                'type'    => 1,
            ],
        ];
        if ($owner === false) {
            $options['conditions']['type'] = 0;
        }
        $res = $this->find('first', $options);
        return $res;
    }
}
