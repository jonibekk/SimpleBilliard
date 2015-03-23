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

    function getCollaboGoalList($user_id, $with_owner = false, $limit = null, $page = 1)
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
                'goal_id'
            ],
            'page'       => $page,
            'limit'      => $limit
        ];
        if ($with_owner) {
            unset($options['conditions']['type']);
            $options['OR'] = [
                'type' => [
                    Collaborator::TYPE_COLLABORATOR,
                    Collaborator::TYPE_OWNER,
                ],
            ];
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

    function getCollaboGoalDetail($team_id, $goal_user_id, $approval_flg)
    {
        $options = [
            'fields'     => ['id', 'type', 'role', 'priority', 'valued_flg'],
            'conditions' => [
                'Collaborator.team_id'    => $team_id,
                'Collaborator.user_id'    => $goal_user_id,
                'Collaborator.valued_flg' => $approval_flg,
            ],
            'contain'    => [
                'Goal' => [
                    'fields'       => [
                        'name', 'goal_category_id', 'end_date', 'photo_file_name',
                        'value_unit', 'target_value', 'start_value', 'description'
                    ],
                    'Purpose'      => ['fields' => 'name'],
                    'GoalCategory' => ['fields' => 'name'],
                ],
                'User' => [
                    'fields' => $this->User->profileFields
                ],
            ],
            'type'       => 'inner',
            'order'      => ['Collaborator.created'],
        ];
        return $this->find('all', $options);
    }

    function changeApprovalStatus($id, $status)
    {
        $this->id = $id;
        $this->save(['valued_flg' => $status]);
    }

    function countCollaboGoal($team_id, $user_id, $goal_user_id, $approval_flg)
    {
        $options = [
            'fields'     => ['id'],
            'conditions' => [
                'Collaborator.team_id'    => $team_id,
                'Collaborator.user_id'    => $goal_user_id,
                'Collaborator.valued_flg' => $approval_flg,
                'User.id !='              => $user_id
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
        return $this->find('count', $options);
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

    //TODO ハードコーディング中! for こーへーさん
    function tempCountUnvalued($team_id = [1,1111111])
    {
        $options = [
            'conditions' => [
                'team_id'    => $team_id,
                'valued_flg' => 0,
            ],
            'fields'     => [
                'user_id'
            ],
            'contain'    => [
                'User' => [
                    'fields'     => [
                        'User.last_name',
                        'User.first_name',
                    ],
                    'TeamMember' => [
                        'conditions' => [
                            'team_id' => $team_id,
                            'evaluation_enable_flg' => 1,
                            'NOT'                   => [
                                'TeamMember.coach_user_id' => null,
                            ],
                        ],
                        'fields'     => [
                            'TeamMember.coach_user_id',
                            'evaluation_enable_flg'
                        ],
                    ],
                ],
            ],
        ];
        $data = $this->find('all', $options);

        $i = 0;
        foreach ($data as $collabo) {
            if (!empty($collabo['User']['TeamMember'])) {
                $res[$i] = [];
                $coach = $this->User->findById($collabo['User']['TeamMember'][0]['coach_user_id']);
                $res[$i] += ['評価対象者' => $collabo['User']['display_last_name'] . $collabo['User']['display_first_name']];
                $res[$i] += ['コーチ' => $coach['User']['display_last_name'] . $coach['User']['display_first_name']];
                $i++;
            }
        }
        $res['count'] = $i;
        return $res;
    }

}
