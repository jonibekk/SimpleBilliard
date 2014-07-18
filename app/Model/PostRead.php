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
        //既読済みのリスト取得
        $options = [
            'conditions' => [
                'post_id' => $post_list,
                'user_id' => $this->me['id'],
            ],
            'fields'     => ['post_id']
        ];
        $read = $this->find('all', $options);

        /** @noinspection PhpDeprecationInspection */
        $read_list = Set::combine($read, '{n}.PostRead.post_id', '{n}.PostRead.post_id');
        $common_data = [
            'user_id' => $this->me['id'],
            'team_id' => $this->current_team_id
        ];
        $post_data = [];
        if (is_array($post_list)) {
            foreach ($post_list as $post_id) {
                //既読をスキップ
                if (in_array($post_id, $read_list)) {
                    continue;
                }
                $data = array_merge($common_data, ['post_id' => $post_id]);
                $post_data[] = $data;
            }
        }
        elseif (!in_array($post_list, $read_list)) {
            $data = array_merge($common_data, ['post_id' => $post_list]);
            $post_data[] = $data;
        }
        if (empty($post_data)) {
            return;
        }
        $this->saveAll($post_data);
    }
}
