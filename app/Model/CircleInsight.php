<?php
App::uses('AppModel', 'Model');

/**
 * CircleInsight Model
 *
 * @property Team   $Team
 * @property Circle $Circle
 */
class CircleInsight extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_count' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'    => [
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
        'Team',
        'Circle',
    ];

    /**
     * サークルの集計データを返す
     *
     * @param string $start_date 集計開始日 YYYY-MM-DD
     * @param string $end_date   集計終了日 YYYY-MM-DD
     * @param int    $timezone   time offset
     *
     * @return array|null
     */
    public function getTotal($start_date, $end_date, $timezone)
    {
        $options = [
            'fields'     => [
                'CircleInsight.circle_id',
                'Circle.name',
                'MAX(CircleInsight.member_count) as max_member_count',
                'SUM(CircleInsight.post_count) as sum_post_count',
                'SUM(CircleInsight.post_read_count) as sum_post_read_count',
                'SUM(CircleInsight.post_like_count) as sum_post_like_count',
                'SUM(CircleInsight.comment_count) as sum_comment_count',
            ],
            'conditions' => [
                'CircleInsight.team_id'        => $this->current_team_id,
                'CircleInsight.target_date >=' => $start_date,
                'CircleInsight.target_date <=' => $end_date,
                'CircleInsight.timezone'       => $timezone,
            ],
            'group'      => 'CircleInsight.circle_id',
            'order'      => ['max_member_count' => 'DESC'],
            'contain'    => ['Circle'],
        ];
        return $this->find('all', $options);
    }
}
