<?php
App::uses('AppModel', 'Model');

/**
 * AccessUser Model
 *
 * @property Team $Team
 * @property User $User
 */
class AccessUser extends AppModel
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
        'Team',
        'User',
    ];

    /**
     * ユーザーIDのリストを返す
     *
     * @param $team_id
     * @param $access_date
     * @param $timezone
     *
     * @return array|null
     */
    public function getUserList($team_id, $access_date, $timezone)
    {
        return $this->find('list', [
            'fields'     => [
                'AccessUser.user_id',
                'AccessUser.user_id',  // key, value 両方 user_id
            ],
            'conditions' => [
                'AccessUser.team_id'     => $team_id,
                'AccessUser.access_date' => $access_date,
                'AccessUser.timezone'    => $timezone,
            ],
        ]);
    }

    /**
     * サイトにアクセスしたユーザーのカウント数を返す
     *
     * @param       $start_date
     * @param       $end_date
     * @param       $timezone
     * @param array $params
     *
     * @return array|null
     */
    public function getUniqueUserCount($start_date, $end_date, $timezone, $params = [])
    {
        $params = array_merge(['user_id' => null], $params);

        $options = [
            'fields'     => [
                'COUNT(DISTINCT user_id) as cnt',
            ],
            'conditions' => [
                'AccessUser.team_id'        => $this->current_team_id,
                'AccessUser.access_date >=' => $start_date,
                'AccessUser.access_date <=' => $end_date,
                'AccessUser.timezone'       => $timezone,
            ],
        ];
        if ($params['user_id'] !== null) {
            $options['conditions']['AccessUser.user_id'] = $params['user_id'];
        }
        $row = $this->find('first', $options);

        $count = 0;
        if (isset($row[0]['cnt'])) {
            $count = $row[0]['cnt'];
        }
        return $count;
    }
}
