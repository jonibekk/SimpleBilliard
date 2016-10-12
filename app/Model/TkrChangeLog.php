<?php
App::uses('AppModel', 'Model');

/**
 * TkrChangeLog Model
 *
 * @property Team      $Team
 * @property Goal      $Goal
 * @property KeyResult $KeyResult
 * @property User      $User
 */
class TkrChangeLog extends AppModel
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
        'KeyResult',
        'User',
    ];

    /**
     * TKRのスナップショットをログに保存する
     * dataフィールドにはmaspackした上でbase64_encodeして格納する。
     *
     * @param $goalId
     * @param $userId
     *
     * @return bool|mixed
     */
    function saveSnapshot($goalId, $userId)
    {
        $keyResult = Hash::get($this->KeyResult->findByGoalId($goalId), 'KeyResult');
        if (empty($keyResult)) {
            return false;
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        $keyResultData = msgpack_pack($keyResult);
        $data = [
            'user_id'       => $userId,
            'team_id'       => $this->current_team_id,
            'key_result_id' => $keyResultData['id'],
            'goal_id'       => $goalId,
            'data'          => base64_encode($keyResultData),
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
            ]
        ]);

        $data = Hash::extract($data, 'TkrChangeLog');

        if (empty($data)) {
            return null;
        }

        /** @noinspection PhpUndefinedFunctionInspection */
        $data['data'] = msgpack_unpack(base64_decode($data['data']));
        return $data;
    }

}
