<?php
App::uses('AppModel', 'Model');

/**
 * Follower Model
 *
 * @property Team      $Team
 * @property KeyResult $KeyResult
 * @property User      $User
 */
class Follower extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
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
        'KeyResult',
        'User',
    ];

    function addFollower($key_result_id)
    {
        $data = [
            'key_result_id' => $key_result_id,
            'user_id'       => $this->my_uid,
            'team_id'       => $this->current_team_id,
        ];
        //既にフォロー済みの場合は処理しない
        if ($this->find('first', ['conditions' => $data])) {
            return false;
        }
        return $this->save($data);
    }

    function deleteFollower($key_result_id)
    {
        $conditions = [
            'Follower.key_result_id' => $key_result_id,
            'Follower.user_id'       => $this->my_uid,
            'Follower.team_id'       => $this->current_team_id,
        ];
        $this->deleteAll($conditions);
        return true;
    }

    function getFollowList($user_id)
    {
        $options = [
            'conditions' => [
                'user_id' => $user_id,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'key_result_id'
            ],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

}
