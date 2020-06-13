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

    /**
     * TODO:サービスに移行する為将来的に削除
     *
     * @deprecated
     *
     * @param $goal_id
     *
     * @return bool|mixed
     */
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
        Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_FOLLOW_GOALS, true), 'user_data');
        return $this->save($data);
    }

    /**
     * フォロー
     *
     * @param $goalId
     * @param $userId
     *
     * @return bool|mixed
     */
    function add($goalId, $userId)
    {
        $data = [
            'goal_id' => $goalId,
            'user_id' => $userId,
            'team_id' => $this->current_team_id,
        ];
        return $this->save($data);
    }

    /**
     * TODO:サービスに移行する為将来的に削除
     *
     * @deprecated
     *
     * @param $goal_id
     *
     * @return bool|mixed
     */
    function deleteFollower($goal_id)
    {
        $conditions = [
            'Follower.goal_id' => $goal_id,
            'Follower.user_id' => $this->my_uid,
            'Follower.team_id' => $this->current_team_id,
        ];
        $this->deleteAll($conditions);
        Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_FOLLOW_GOALS, true), 'user_data');
        return true;
    }

    /**
     * フォロー解除
     *
     * @param int|null|string $goalId
     * @param bool            $userId
     *
     * @return bool|mixed
     */
    function del($goalId, $userId)
    {
        $conditions = [
            'Follower.goal_id' => $goalId,
            'Follower.user_id' => $userId,
            'Follower.team_id' => $this->current_team_id,
        ];
        return $this->deleteAll($conditions);
    }

    function getFollowList($user_id, $limit = null, $page = 1)
    {
        $is_default = false;
        if ($user_id == $this->my_uid && $limit === null && $page === 1) {
            $is_default = true;
            $res = Cache::read($this->getCacheKey(CACHE_KEY_CHANNEL_FOLLOW_GOALS, true, $user_id), 'user_data');
            if ($res !== false) {
                return $res;
            }
        }

        $options = [
            'conditions' => [
                'user_id' => $user_id,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'goal_id',
                'goal_id'
            ],
            'page'       => $page,
            'limit'      => $limit
        ];
        $res = $this->find('list', $options);

        if ($is_default) {
            Cache::write($this->getCacheKey(CACHE_KEY_CHANNEL_FOLLOW_GOALS, true, $user_id), $res, 'user_data');
        }
        return $res;
    }

    function isExists($goal_id, $user_id = null, $team_id = null)
    {
        $options = [
            'conditions' => [
                'user_id' => $user_id ? $user_id : $this->my_uid,
                'goal_id' => $goal_id,
                'team_id' => $team_id ? $team_id : $this->current_team_id,
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
        $params = array_merge([
            'limit'      => null,
            'page'       => 1,
            'order'      => ['Follower.created' => 'DESC'],
            'with_group' => false,
        ], $params);

        $options = [
            'conditions' => [
                'Follower.goal_id' => $goal_id,
                'Follower.team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'User' => [
                    'fields' => [
                        'User.id',
                        'User.*',
                    ]
                ]
            ],
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
                    'MemberGroup.team_id'   => $this->current_team_id,
                ],
            ];
            $options['joins'][] = [
                'type'       => 'LEFT',
                'table'      => 'groups',
                'alias'      => 'Group',
                'conditions' => [
                    'Group.id = MemberGroup.group_id',
                    'Group.team_id' => $this->current_team_id,
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
                'user_id',
                'user_id'
            ],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * ゴールIDごとの件数取得
     *
     * @param $goalIds
     *
     * @return bool
     */
    public function countEachGoalId($goalIds)
    {
        $ret = $this->find('all', [
            'fields'     => ['goal_id', 'COUNT(goal_id) as cnt'],
            'conditions' => ['goal_id' => $goalIds],
            'group'      => ['goal_id'],
        ]);
        // 0件のゴールも配列要素を作り、値を0として返す
        $defaultCountEachGoalId = array_fill_keys($goalIds, 0);
        $ret = Hash::combine($ret, '{n}.Follower.goal_id', '{n}.0.cnt');
        return $ret + $defaultCountEachGoalId;
    }

    /**
     * ゴールごとにフォローしているか判定
     *
     * @param $goalIds
     *
     * @return bool
     */
    public function isFollowingEachGoalId($goalIds, $userId)
    {
        $ret = $this->find('all', [
            'fields'     => ['goal_id', 'count(goal_id) as exist'],
            'conditions' => [
                'goal_id' => $goalIds,
                'user_id' => $userId
            ],
            'group'      => ['goal_id'],
        ]);
        // 0件のゴールも配列要素を作り、値を0として返す
        $defaultEachGoalId = array_fill_keys($goalIds, 0);
        $ret = Hash::combine($ret, '{n}.Follower.goal_id', '{n}.0.exist');
        return $ret + $defaultEachGoalId;
    }

    /**
     * ユニークのフォロー情報取得
     *
     * @param $goalId
     * @param $userId
     *
     * @return array
     */
    public function getUnique($goalId, $userId)
    {
        $ret = $this->find('first', [
            'conditions' => [
                'goal_id' => $goalId,
                'user_id' => $userId,
                'team_id' => $this->current_team_id,
            ]
        ]);
        return Hash::get($ret, 'Follower');
    }
}
