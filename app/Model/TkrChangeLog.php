<?php
App::uses('AppModel', 'Model');

/**
 * TkrChangeLog Model
 *
 * @property Goal      $Goal
 * @property KeyResult $KeyResult
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
     * TKRのスナップショットをログに保存する
     * dataフィールドにはmaspackした上でbase64_encodeして格納する。
     *
     * @param $goalId
     *
     * @return bool|mixed
     */
    function saveSnapshot($goalId)
    {
        $keyResult = Hash::get($this->KeyResult->getTkr($goalId), 'KeyResult');
        if (empty($keyResult)) {
            return false;
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        $keyResultData = msgpack_pack($keyResult);
        $data = [
            'team_id'       => $this->current_team_id,
            'key_result_id' => $keyResult['id'],
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
