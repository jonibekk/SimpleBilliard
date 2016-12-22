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
     * ログタイプ
     */
    const TYPE_MODIFY = 0;
    const TYPE_APPROVAL_BY_COACH = 1;

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
    function saveSnapshot(int $userId, int $krId, int $type)
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
            'type'          => $type
        ];
        $this->create();
        if (!$this->save($data)) {
            return false;
        }
        return $this->getLastInsertID();
    }

    /**
     * ゴールの最新のスナップショットを取得
     *
     * @param $goalId
     *
     * @return array|null
     */
    function getLatestSnapshot($goalId, $type)
    {
        $data = $this->find('first', [
            'conditions' => [
                'goal_id' => $goalId,
                'type'    => $type
            ],
            'order'      => ['id' => 'desc']
        ]);

        $data = Hash::extract($data, 'KrChangeLog');

        if (empty($data)) {
            return null;
        }

        /** @noinspection PhpUndefinedFunctionInspection */
        return msgpack_unpack(base64_decode($data['data']));
    }
}
