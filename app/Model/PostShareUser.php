<?php
App::uses('AppModel', 'Model');

/**
 * PostShareUser Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class PostShareUser extends AppModel
{
    /**
     * 現状、このタイプを内部的に利用しない。
     * ユーザに共有されたものか通知されたものかを切り分ける目的。
     */
    const SHARE_TYPE_SHARED = 0;
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
        'User',
        'Team',
    ];

    public function add($post_id, $users, $team_id = null, $share_type = self::SHARE_TYPE_SHARED)
    {
        if (empty($users)) {
            return false;
        }
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $data = [];
        foreach ($users as $uid) {
            $data[] = [
                'user_id'    => $uid,
                'post_id'    => $post_id,
                'team_id'    => $team_id,
                'share_type' => $share_type,
            ];
        }
        return $this->saveAll($data);
    }

    /**
     * 自分に共有された投稿のID一覧を返す
     *
     * @param        $start
     * @param        $end
     * @param string $order
     * @param string $order_direction
     * @param int    $limit
     * @param array  $params
     *                 'user_id' : 指定すると投稿者で絞る
     *
     * @return array|null
     */
    public function getShareWithMeList($start, $end, $order = "PostShareUser.modified", $order_direction = "desc", $limit = 1000, array $params = [])
    {
        // パラメータデフォルト
        $params = array_merge(['user_id' => null], $params);

        $backupPrimaryKey = $this->primaryKey;
        $this->primaryKey = 'post_id';

        $options = [
            'conditions' => [
                'PostShareUser.user_id'                  => $this->my_uid,
                'PostShareUser.team_id'                  => $this->current_team_id,
                'PostShareUser.modified BETWEEN ? AND ?' => [$start, $end],
            ],
            'order'      => [$order => $order_direction],
            'limit'      => $limit,
            'fields'     => ['PostShareUser.post_id'],
            'contain'    => [],
        ];
        if ($params['user_id'] !== null) {
            $options['conditions']['Post.user_id'] = $params['user_id'];
            $options['contain'][] = 'Post';
        }
        $res = $this->find('list', $options);
        $this->primaryKey = $backupPrimaryKey;

        return $res;
    }

    public function isShareWithMe($post_id)
    {
        $backupPrimaryKey = $this->primaryKey;
        $this->primaryKey = 'post_id';

        $options = [
            'conditions' => [
                'post_id' => $post_id,
                'user_id' => $this->my_uid,
                'team_id' => $this->current_team_id,
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

    public function getShareUsersByPost($post_id)
    {
        $options = [
            'conditions' => [
                'PostShareUser.post_id' => $post_id,
                'PostShareUser.team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'User' => ['fields' => $this->User->profileFields]
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    public function getShareUserListByPost($post_id)
    {
        error_log("-----FURU:post:$post_id\n",3,"/tmp/hoge.log");
        $primary_backup = $this->primaryKey;
        $this->primaryKey = 'user_id';
        $options = [
            'conditions' => [
                'PostShareUser.post_id' => $post_id,
                'PostShareUser.team_id' => $this->current_team_id,
            ],
            'fields'     => ['PostShareUser.user_id', 'PostShareUser.user_id']
        ];
        $res = $this->find('list', $options);
        $this->primaryKey = $primary_backup;
        error_log("-----FURU:post2:".print_r($res,true)."\n",3,"/tmp/hoge.log");
        return $res;
    }

}
