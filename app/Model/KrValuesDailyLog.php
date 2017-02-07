<?php
App::uses('AppModel', 'Model');

/**
 * KrValuesDailyLog Model
 */
class KrValuesDailyLog extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'current_value' => [
            'decimal' => [
                'rule' => ['decimal'],
            ],
        ],
        'start_value'   => [
            'decimal' => [
                'rule' => ['decimal'],
            ],
        ],
        'target_value'  => [
            'decimal' => [
                'rule' => ['decimal'],
            ],
        ],
        'priority'      => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'       => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * 指定したチームの日次データが存在するか判定
     * @param  int    $teamId
     * @param  string $targetDate
     * @return bool
     */
    function existTeamLog(int $teamId, string $targetDate): bool
    {
        $options = [
            'conditions' => [
                'team_id'     => $teamId,
                'target_date' => $targetDate
            ],
            'fields'     => ['id']
        ];
        $ret = $this->find('first', $options);
        return (bool)$ret;
    }
}
