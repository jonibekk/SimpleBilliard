<?php
App::uses('AppModel', 'Model');

/**
 * Collaborator Model
 *
 * @property Team      $Team
 * @property KeyResult $KeyResult
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

    function add($kr_id, $uid = null, $type = self::TYPE_COLLABORATOR)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $skr_user = [
            'team_id'       => $this->current_team_id,
            'user_id'       => $uid,
            'type'          => $type,
            'key_result_id' => $kr_id,
        ];
        $res = $this->save($skr_user);
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
        $this->KeyResult->Follower->deleteFollower($data['Collaborator']['key_result_id']);
        return $res;
    }

    function getCollaboKeyResultList($user_id)
    {
        $options = [
            'conditions' => [
                'user_id' => $user_id,
                'team_id' => $this->current_team_id,
                'type'    => Collaborator::TYPE_COLLABORATOR,
            ],
            'fields'     => [
                'key_result_id'
            ],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    function isCollaborated($key_result_id, $uid = null)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $options = [
            'conditions' => [
                'Collaborator.key_result_id' => $key_result_id,
                'Collaborator.user_id'       => $uid,
            ],
        ];
        $res = $this->find('first', $options);
        if (!empty($res)) {
            return true;
        }
        return false;
    }

}
