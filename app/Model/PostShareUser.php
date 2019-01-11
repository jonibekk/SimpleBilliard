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

    public function isShareWithMe($postId, $userId = null, $teamId = null)
    {
        $userId = $userId ?: $this->my_uid;
        $teamId = $teamId ?: $this->current_team_id;

        $backupPrimaryKey = $this->primaryKey;
        $this->primaryKey = 'post_id';

        $options = [
            'conditions' => [
                'post_id' => $postId,
                'user_id' => $userId,
                'team_id' => $teamId,
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
            'fields'     => ['PostShareUser.user_id', 'PostShareUser.user_id']
        ];
        $res = $this->find('list', $options);
        $this->primaryKey = $primary_backup;
        return $res;
    }

    public function getPostIdListByUserId($user_id)
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
                'user_id' => $user_id
            ],
            'fields'     => ['post_id']
        ];
        return $this->find('list', $options);
    }

}
