<?php
App::uses('AppModel', 'Model');

/**
 * GoalChangeLog Model
 *
 * @property Goal $Goal
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
            'notBlank' => [
                'rule' => ['notBlank'],
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
        'Goal',
    ];

    /**
     * ゴールのスナップショットをログに保存する
     * dataフィールドにはmaspackした上でbase64_encodeして格納する。
     *
     * @param $goalId
     *
     * @return bool|mixed
     */
    function saveSnapshot($goalId)
    {
        $goal = $this->Goal->find('first', ['conditions' => ['id' => $goalId]]);
        $goal = Hash::get($goal, 'Goal');
        if (empty($goal)) {
            return false;
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        $goalData = msgpack_pack($goal);
        $data = [
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
     *
     * @return array|null
     */
    function findLatestSnapshot($goalId)
    {
        $data = $this->find('first', [
            'conditions' => [
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
