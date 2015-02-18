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
        'Goal',
        'User',
    ];

    function addFollower($goal_id)
    {
        $data = [
            'goal_id' => $goal_id,
            'user_id' => $this->my_uid,
            'team_id' => $this->current_team_id,
        ];
        //既にフォロー済みの場合は処理しない
        if ($this->find('first', ['conditions' => $data])) {
            return false;
        }
        return $this->save($data);
    }

    function deleteFollower($goal_id)
    {
        $conditions = [
            'Follower.goal_id' => $goal_id,
            'Follower.user_id' => $this->my_uid,
            'Follower.team_id' => $this->current_team_id,
        ];
        $this->deleteAll($conditions);
        return true;
    }

    function getFollowList($user_id, $limit=null, $page=1)
    {
        $options = [
            'conditions' => [
                'user_id' => $user_id,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'goal_id'
            ],
            'page'  => $page,
            'limit' => $limit
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    function isFollowed($goal_id, $uid = null)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $options = [
            'conditions' => [
                'Follower.goal_id' => $goal_id,
                'Follower.user_id' => $uid,
            ],
        ];
        $res = $this->find('first', $options);
        if (!empty($res)) {
            return true;
        }
        return false;
    }

    function isExists($goal_id)
    {
        $options = [
            'conditions' => [
                'user_id' => $this->my_uid,
                'goal_id' => $goal_id,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'id'
            ],
        ];
        $res = $this->find('first', $options);
        return $res;
    }

}
