<?php
App::uses('AppModel', 'Model');

/**
 * Follower Model
 *
 * @property Team      $Team
 * @property KeyResult $KeyResult
 * @property Goal      $Goal
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

    function getFollowList($user_id, $limit = null, $page = 1)
    {
        $options = [
            'conditions' => [
                'user_id' => $user_id,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'goal_id', 'goal_id'
            ],
            'page'       => $page,
            'limit'      => $limit
        ];
        $res = $this->find('list', $options);
        return $res;
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

    /**
     * フォロワーの一覧を取得
     *
     * @param       $goal_id
     * @param array $params
     *                'limit' : find() の limit
     *                'page'  : find() の page
     *                'order' : find() の order
     *                'with_group' : true にするとグループ１の情報を含める
     *
     * @return array|null
     */
    public function getFollowerByGoalId($goal_id, array $params = [])
    {
        // パラメータデフォルト
        $params = array_merge(['limit'      => null,
                               'page'       => 1,
                               'order'      => ['Follower.created' => 'DESC'],
                               'with_group' => false,
                              ], $params);

        $options = [
            'conditions' => [
                'Follower.goal_id' => $goal_id,
                'Follower.team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'Follower.*',
                'User.*',
            ],
            'contain'    => ['User'],
            'limit'      => $params['limit'],
            'page'       => $params['page'],
            'order'      => $params['order'],
            'joins'      => [],
        ];

        if ($params['with_group']) {
            // グループ１の情報だけ join する
            $options['joins'][] = [
                'type'       => 'LEFT',
                'table'      => 'member_groups',
                'alias'      => 'MemberGroup',
                'conditions' => [
                    'MemberGroup.user_id = User.id',
                    'MemberGroup.index_num' => 0,
                ],
            ];
            $options['joins'][] = [
                'type'       => 'LEFT',
                'table'      => 'groups',
                'alias'      => 'Group',
                'conditions' => [
                    'Group.id = MemberGroup.group_id',
                ],
            ];
            $options['fields'][] = 'Group.*';
        }
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * @param $goal_id
     *
     * @return array|null
     */
    function getFollowerListByGoalId($goal_id)
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
        $res = $this->find('list', $options);
        return $res;
    }

}
