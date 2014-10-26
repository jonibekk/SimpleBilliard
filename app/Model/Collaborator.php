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

    function getCollaboGoalList($user_id)
    {
        $options = [
            'conditions' => [
                'user_id' => $user_id,
                'team_id' => $this->current_team_id,
                'type'    => Collaborator::TYPE_COLLABORATOR,
            ],
            'fields'     => [
                'goal_id'
            ],
        ];
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

}
