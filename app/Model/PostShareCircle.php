<?php
App::uses('AppModel', 'Model');

/**
 * PostShareCircle Model
 *
 * @property Post   $Post
 * @property Circle $Circle
 * @property Team   $Team
 */
class PostShareCircle extends AppModel
{
    //そのユーザのALLフィード、サークルページ両方に表示される
    const SHARE_TYPE_SHARED = 0;
    //そのユーザのALLフィードのみに表示される。サークルページには表示されない
    const SHARE_TYPE_ONLY_NOTIFY = 1;

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
        'Post',
        'Circle',
        'Team',
    ];

    public function add($post_id, $circles, $team_id = null, $share_type = self::SHARE_TYPE_SHARED)
    {
        if (empty($circles)) {
            return false;
        }
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $data = [];
        foreach ($circles as $circle_id) {
            $data[] = [
                'circle_id'  => $circle_id,
                'post_id'    => $post_id,
                'team_id'    => $team_id,
                'share_type' => $share_type,
            ];
        }
        return $this->saveAll($data);
    }

    public function getMyCirclePostList($start, $end, $order = "modified", $order_direction = "desc", $limit = 1000, $my_circle_list = null, $share_type = null)
    {
        if (!$my_circle_list) {
            $my_circle_list = $this->Circle->CircleMember->getMyCircleList(true);
        }
        $backupPrimaryKey = $this->primaryKey;
        $this->primaryKey = 'post_id';
        $options = [
            'conditions' => [
                'circle_id'                => $my_circle_list,
                'team_id'                  => $this->current_team_id,
                'modified BETWEEN ? AND ?' => [$start, $end],
            ],
            'order'      => [$order => $order_direction],
            'limit'      => $limit,
            'fields'     => ['post_id'],
        ];
        if ($share_type !== null) {
            $options['conditions']['share_type'] = $share_type;
        }
        $res = $this->find('list', $options);
        $this->primaryKey = $backupPrimaryKey;
        return $res;
    }

    /**
     * 自分の閲覧可能な投稿のID一覧を返す
     * （公開サークルへの投稿 + 自分が所属している秘密サークルへの投稿）
     *
     * @param        $start
     * @param        $end
     * @param string $order
     * @param string $order_direction
     * @param int    $limit
     * @param array  $params
     *                 'user_id' : 指定すると投稿者IDで絞る
     *
     * @return array|null
     */
    public function getAccessibleCirclePostList($start, $end, $order = "PostShareCircle.modified", $order_direction = "desc", $limit = 1000, array $params = [])
    {
        // パラメータデフォルト
        $params = array_merge(['user_id' => null], $params);

        $my_circle_list = $this->Circle->CircleMember->getMyCircleList();
        $options = [
            'conditions' => [
                'OR'                                       => [
                    'PostShareCircle.circle_id' => $my_circle_list,
                    'Circle.public_flg'         => 1
                ],
                'PostShareCircle.team_id'                  => $this->current_team_id,
                'PostShareCircle.modified BETWEEN ? AND ?' => [$start, $end],
            ],
            'order'      => [$order => $order_direction],
            'limit'      => $limit,
            'fields'     => ['PostShareCircle.post_id', 'PostShareCircle.post_id'],
            'contain'    => ['Circle'],
        ];
        if ($params['user_id'] !== null) {
            $options['conditions']['Post.user_id'] = $params['user_id'];
            $options['contain'][] = 'Post';
        }
        $res = $this->find('list', $options);
        return $res;
    }

    public function isMyCirclePost($post_id)
    {
        $my_circle_list = $this->Circle->CircleMember->getMyCircleList();
        $backupPrimaryKey = $this->primaryKey;
        $this->primaryKey = 'post_id';
        $options = [
            'conditions' => [
                'post_id'   => $post_id,
                'circle_id' => $my_circle_list,
                'team_id'   => $this->current_team_id,
            ],
            'fields'     => ['post_id'],
        ];
        $res = $this->find('list', $options);
        $this->primaryKey = $backupPrimaryKey;
        if (!empty($res)) {
            return true;
        }
        return false;
    }

    /**
     * $post_id の投稿が公開サークルに共有されているか確認する
     *
     * @param $post_id
     *
     * @return bool 公開サークルに共有されている時 true
     */
    public function isShareWithPublicCircle($post_id)
    {
        $options = [
            'conditions' => [
                'PostShareCircle.post_id' => $post_id,
                'PostShareCircle.team_id' => $this->current_team_id,
                'Circle.public_flg'       => 1,
            ],
            'contain'    => [
                'Circle',
            ]
        ];
        $res = $this->find('first', $options);
        return $res ? true : false;
    }

    public function getShareCirclesAndMembers($post_id)
    {
        $circle_list = $this->getShareCircleList($post_id);
        $res = $this->Circle->getCirclesAndMemberById($circle_list);
        return $res;
    }

    public function getShareCircleList($post_id)
    {
        $options = [
            'conditions' => [
                'PostShareCircle.post_id' => $post_id,
                'PostShareCircle.team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'PostShareCircle.circle_id',
            ],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    public function getShareCircleMemberList($post_id)
    {
        $circle_list = $this->getShareCircleList($post_id);
        $res = $this->Circle->CircleMember->getMemberList($circle_list, true);
        return $res;
    }

}
