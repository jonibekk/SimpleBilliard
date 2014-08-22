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

    public function add($post_id, $users, $team_id = null)
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
                'user_id' => $uid,
                'post_id' => $post_id,
                'team_id' => $team_id,
            ];
        }
        return $this->saveAll($data);
    }

    public function getShareWithMeList($start, $end, $order = "modified", $order_direction = "desc", $limit = 1000)
    {
        $backupPrimaryKey = $this->primaryKey;
        $this->primaryKey = 'post_id';

        $options = [
            'conditions' => [
                'user_id'                  => $this->my_uid,
                'team_id'                  => $this->current_team_id,
                'modified BETWEEN ? AND ?' => [$start, $end],
            ],
            'order'      => [$order => $order_direction],
            'limit'      => $limit,
            'fields'     => ['post_id'],
        ];
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
        $primary_backup = $this->primaryKey;
        $this->primaryKey = 'user_id';
        $options = [
            'conditions' => [
                'PostShareUser.post_id' => $post_id,
                'PostShareUser.team_id' => $this->current_team_id,
            ],
            'fields'     => ['PostShareUser.user_id']
        ];
        $res = $this->find('list', $options);
        $this->primaryKey = $primary_backup;
        return $res;
    }

}
