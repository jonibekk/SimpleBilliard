<?php
App::uses('AppModel', 'Model');

/**
 * PostRead Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class PostRead extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => ['boolean' => ['rule' => ['boolean'],],],
    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Post' => [
            "counterCache" => true,
            'counterScope' => ['PostRead.del_flg' => false]
        ],
        'User',
        'Team',
    ];

    public function red($post_list)
    {
        //既読投稿を除外
        $post_list = $this->pickUnreadPosts($post_list);
        //自分の投稿を除外
        $post_list = $this->pickUnMyPosts($post_list);
        $common_data = [
            'user_id' => $this->my_uid,
            'team_id' => $this->current_team_id
        ];
        $post_data = [];
        if (is_array($post_list)) {
            foreach ($post_list as $post_id) {
                $data = array_merge($common_data, ['post_id' => $post_id]);
                $post_data[] = $data;
            }
        }
        if (empty($post_data)) {
            return;
        }
        $res = $this->saveAll($post_data);
        return $res;
    }

    private function pickUnreadPosts($post_list)
    {
        //既読済みのリスト取得
        $options = [
            'conditions' => [
                'post_id' => $post_list,
                'user_id' => $this->my_uid,
                'team_id' => $this->current_team_id,
            ],
            'fields'     => ['post_id']
        ];
        $read = $this->find('all', $options);
        $read_list = Hash::combine($read, '{n}.PostRead.post_id', '{n}.PostRead.post_id');
        $unread_posts = [];
        if (is_array($post_list)) {
            foreach ($post_list as $post_id) {
                //既読をスキップ
                if (in_array($post_id, $read_list)) {
                    continue;
                }
                $unread_posts[$post_id] = $post_id;
            }
        }
        elseif (!in_array($post_list, $read_list)) {
            $unread_posts[$post_list] = $post_list;
        }
        return $unread_posts;
    }

    private function pickUnMyPosts($post_list)
    {
        if (empty($post_list)) {
            return;
        }
        //自分以外の投稿を取得
        $options = [
            'conditions' => [
                'id'      => $post_list,
                'team_id' => $this->current_team_id,
                'NOT'     => [
                    'user_id' => $this->my_uid,
                ]
            ],
            'fields'     => ['id']
        ];
        $un_my_posts = $this->Post->find('all', $options);
        $un_my_posts = Hash::combine($un_my_posts, '{n}.Post.id', '{n}.Post.id');
        return $un_my_posts;
    }

    public function getRedUsers($post_id)
    {
        $options = [
            'conditions' => [
                'PostRead.post_id' => $post_id,
                'PostRead.team_id' => $this->current_team_id,
            ],
            'order'      => [
                'PostRead.created' => 'desc'
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
