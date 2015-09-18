<?php
App::uses('AppModel', 'Model');

/**
 * GroupInsight Model
 *
 * @property Team  $Team
 * @property Group $Group
 */
class GroupInsight extends AppModel
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
        'Group',
    ];

    /**
     * $start_date から $end_date の集計結果を返す
     *
     * @param $group_id
     * @param $start_date
     * @param $end_date
     * @param $timezone
     *
     * @return array|null
     */
    public function getTotal($group_id, $start_date, $end_date, $timezone)
    {
        $options = [
            'fields'     => [
                'MAX(user_count) as max_user_count',
            ],
            'conditions' => [
                'GroupInsight.team_id'        => $this->current_team_id,
                'GroupInsight.group_id'       => $group_id,
                'GroupInsight.target_date >=' => $start_date,
                'GroupInsight.target_date <=' => $end_date,
                'GroupInsight.timezone'       => $timezone,
            ],
        ];
        return $this->find('first', $options);
    }
}
