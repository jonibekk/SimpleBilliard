<?php
App::uses('AppModel', 'Model');

/**
 * PostLike Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class PostLike extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'post_id' => ['numeric' => ['rule' => ['numeric'], 'allowEmpty' => false],],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Post' => [
            "counterCache" => true,
            'counterScope' => ['PostLike.del_flg' => false]
        ],
        'User',
        'Team',
    ];

    public function changeLike($post_id)
    {
        $res = [
            'created' => false,
            'error'   => false,
            'count'   => 0
        ];

        $exists = $this->find('first', ['conditions' => ['post_id' => $post_id, 'user_id' => $this->me['id']]]);
        if (isset($exists['PostLike']['id'])) {
            $this->delete($exists['PostLike']['id']);
            $this->updateCounterCache(['post_id' => $exists['PostLike']['id']]);
        }
        else {
            $data = [
                'user_id' => $this->me['id'],
                'team_id' => $this->current_team_id,
                'post_id' => $post_id
            ];
            if (!$this->save($data)) {
                $res['error'] = true;
            }
            $res['created'] = true;
        }
        $post = $this->Post->read('post_like_count', $post_id);
        if (isset($post['Post']['post_like_count'])) {
            $res['count'] = $post['Post']['post_like_count'];
        }
        return $res;
    }
}
