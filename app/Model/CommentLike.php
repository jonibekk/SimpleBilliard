<?php
App::uses('AppModel', 'Model');

/**
 * CommentLike Model
 *
 * @property Comment $Comment
 * @property User    $User
 * @property Team    $Team
 */
class CommentLike extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg'    => ['boolean' => ['rule' => ['boolean']]],
        'comment_id' => ['numeric' => ['rule' => ['numeric'], 'allowEmpty' => false],],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Comment' => [
            "counterCache" => true,
            'counterScope' => ['CommentLike.del_flg' => false]
        ],
        'User',
        'Team',
    ];

    public function changeLike($comment_id)
    {
        $res = [
            'created' => false,
            'error'   => false,
            'count'   => 0
        ];

        $exists = $this->find('first', ['conditions' => ['comment_id' => $comment_id, 'user_id' => $this->me['id']]]);
        if (isset($exists['CommentLike']['id'])) {
            $this->delete($exists['CommentLike']['id']);
            $this->updateCounterCache(['comment_id' => $exists['CommentLike']['id']]);
        }
        else {
            $data = [
                'user_id'    => $this->me['id'],
                'team_id'    => $this->current_team_id,
                'comment_id' => $comment_id
            ];
            if (!$this->save($data)) {
                $res['error'] = true;
            }
            $res['created'] = true;
        }
        $post = $this->Comment->read('comment_like_count', $comment_id);
        if (isset($post['Comment']['comment_like_count'])) {
            $res['count'] = $post['Comment']['comment_like_count'];
        }
        return $res;
    }

}
