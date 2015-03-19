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

    public function  red($comment_list)
    {
        //既読コメントを除外
        $comment_list = $this->pickUnread($comment_list);
        //自分のコメントを除外
        $comment_list = $this->pickNotMine($comment_list);
        $common_data = [
            'user_id' => $this->my_uid,
            'team_id' => $this->current_team_id
        ];
        $comment_data = [];
        if (is_array($comment_list)) {
            foreach ($comment_list as $comment) {
                $data = array_merge($common_data, ['comment_id' => $comment]);
                $comment_data[] = $data;
            }
        }
        if (empty($comment_data)) {
            return;
        }
        $this->saveAll($comment_data);
    }

    private function pickUnread($comment_list)
    {
        //既読済みのリスト取得
        $options = [
            'conditions' => [
                'id'      => $comment_list,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => ['id'],
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
        $comment_data = [];
        foreach ($all_read as $read) {
            //既読をスキップ
            if (!empty($read['CommentRead'])) {
                continue;
            }
            $comment_data[] = $read['Comment']['id'];
        }
        return $comment_data;
    }

    private function pickNotMine($comment_list)
    {
        //自分以外の投稿を取得
        $options = [
            'conditions' => [
                'id'      => $comment_list,
                'team_id' => $this->current_team_id,
                'NOT'     => [
                    'user_id' => $this->my_uid,
                ]
            ],
            'fields'     => ['id']
        ];
        $not_mine_list = $this->Comment->find('all', $options);
        /** @noinspection PhpDeprecationInspection */
        $not_mines = Set::classicExtract($not_mine_list, '{n}.Comment.id');
        return $not_mines;
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

    function countMyRead($comment_list)
    {
        $options = [
            'conditions' => [
                'CommentRead.comment_id' => $comment_list,
                'CommentRead.user_id'    => $this->my_uid,
                'CommentRead.team_id'    => $this->current_team_id,
            ],
        ];
        $res = $this->find('count', $options);
        return $res;
    }
}
