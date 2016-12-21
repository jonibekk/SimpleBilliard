<?php
App::uses('AppModel', 'Model');

/**
 * KrChangeLog Model
 *
 * @property Goal      $Goal
 * @property KeyResult $KeyResult
 */
class KrChangeLog extends AppModel
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
        'KeyResult',
    ];

    /**
     * KRのスナップショットをログに保存する
     * dataフィールドにはmaspackした上でbase64_encodeして格納する。
     *
     * @param int $userId
     * @param int $krId
     *
     * @return bool|mixed
     * @internal param $goalId
     */
    function saveSnapshot(int $userId, int $krId): bool
    {
        $kr = $this->KeyResult->getById($krId);
        if (empty($kr)) {
            return false;
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        $keyResultData = msgpack_pack($kr);
        $data = [
            'team_id'       => $this->current_team_id,
            'goal_id'       => $kr['goal_id'],
            'user_id'       => $userId,
            'key_result_id' => $krId,
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

        $data = Hash::extract($data, 'TkrChangeLog');

        if (empty($data)) {
            return null;
        }

        /** @noinspection PhpUndefinedFunctionInspection */
        return msgpack_unpack(base64_decode($data['data']));
    }
}
