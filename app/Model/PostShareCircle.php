<?php
App::uses('AppModel', 'Model');

/**
 * PostShareCircle Model
 *
 * @property Post   $Post
 * @property Circle $Circle
 * @property Team   $Team
 */
class PostShareCircle extends AppModel
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
        'Circle',
        'Team',
    ];

    public function add($post_id, $circles, $team_id = null)
    {
        if (empty($circles)) {
            return false;
        }
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $data = [];
        foreach ($circles as $circle_id) {
            $data[] = [
                'circle_id' => $circle_id,
                'post_id'   => $post_id,
                'team_id'   => $team_id,
            ];
        }
        return $this->saveAll($data);

    }

    public function getMyCirclePostList($start, $end, $order = "modified", $order_direction = "desc", $limit = 1000, $my_circle_list = null)
    {
        if (!$my_circle_list) {
            $my_circle_list = $this->Circle->CircleMember->getMyCircleList();
        }
        $backupPrimaryKey = $this->primaryKey;
        $this->primaryKey = 'post_id';
        $options = [
            'conditions' => [
                'circle_id'                => $my_circle_list,
                'team_id'                  => $this->current_team_id,
                'modified BETWEEN ? AND ?' => [$start, $end],
            ],
            'order' => [$order => $order_direction],
            'limit' => $limit,
            'fields'     => ['post_id'],
        ];
        $res = $this->find('list', $options);
        $this->primaryKey = $backupPrimaryKey;
        return $res;
    }

}
