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

    public function getShareWithMeList($start, $end)
    {
        $options = [
            'conditions' => [
                'user_id'                  => $this->me['id'],
                'team_id'                  => $this->current_team_id,
                'modified BETWEEN ? AND ?' => [$start, $end],
            ],
            'fields'     => ['post_id'],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

}
