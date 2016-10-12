<?php
App::uses('AppModel', 'Model');

/**
 * GoalChangeLog Model
 *
 * @property Team $Team
 * @property Goal $Goal
 * @property User $User
 */
class GoalChangeLog extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'data'    => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
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
        'Goal',
        'User',
    ];

    /**
     * ゴールのスナップショットをログに保存する
     * dataフィールドにはmaspackした上でbase64_encodeして格納する。
     *
     * @param $goalId
     * @param $userId
     *
     * @return bool|mixed
     */
    function saveSnapshot($goalId, $userId)
    {
        $goal = Hash::get($this->Goal->findById($goalId), 'Goal');
        if (empty($goal)) {
            return false;
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        $goalData = msgpack_pack($goal);
        $data = [
            'user_id' => $userId,
            'team_id' => $this->current_team_id,
            'goal_id' => $goalId,
            'data'    => base64_encode($goalData),
        ];
        $this->create();
        $ret = $this->save($data);
        return $ret;
    }

    /**
     * ゴールの最新のスナップショットを取得
     *
     * @param $goalId
     * @param $userId
     *
     * @return array|null
     */
    function findLatestSnapshot($goalId, $userId)
    {
        $data = $this->find('first', [
            'conditions' => [
                'user_id' => $userId,
                'goal_id' => $goalId,
            ],
            'order'      => ['id' => 'desc']
        ]);
        $data = Hash::extract($data, 'GoalChangeLog');

        if (empty($data)) {
            return null;
        }

        /** @noinspection PhpUndefinedFunctionInspection */
        $data['data'] = msgpack_unpack(base64_decode($data['data']));
        return $data;
    }

}
