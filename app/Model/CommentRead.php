<?php
App::uses('AppModel', 'Model');

/**
 * CommentRead Model
 *
 * @property Comment $Comment
 * @property User    $User
 * @property Team    $Team
 */
class CommentRead extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => ['boolean' => ['rule' => ['boolean']]],
    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Comment' => [
            "counterCache" => true,
            'counterScope' => ['CommentRead.del_flg' => false]
        ],
        'User',
        'Team'
    ];

    public function red($post_list)
    {
        //既読済みのリスト取得
        $options = [
            'conditions' => [
                'post_id' => $post_list,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => ['id', 'post_id'],
            'contain'    => [
                'CommentRead' => [
                    'conditions' => [
                        'user_id' => $this->my_uid,
                        'team_id' => $this->current_team_id,
                    ],
                    'fields'     => ['id']
                ]
            ]
        ];
        $all_read = $this->Comment->find('all', $options);
        $common_data = [
            'user_id' => $this->my_uid,
            'team_id' => $this->current_team_id
        ];
        $comment_data = [];
        foreach ($all_read as $read) {
            //既読をスキップ
            if (!empty($read['CommentRead'])) {
                continue;
            }
            $data = array_merge($common_data, ['comment_id' => $read['Comment']['id']]);
            $comment_data[] = $data;
        }

        if (empty($comment_data)) {
            return;
        }
        $this->saveAll($comment_data);
    }

    public function getRedUsers($comment_id)
    {
        $options = [
            'conditions' => [
                'CommentRead.comment_id' => $comment_id,
                'CommentRead.team_id'    => $this->current_team_id,
            ],
            'order'      => [
                'CommentRead.created' => 'desc'
            ],
            'contain'    => [
                'User' => [
                    'fields' => $this->User->profileFields
                ],
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

}
