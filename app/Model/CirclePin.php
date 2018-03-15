<?php
App::uses('AppModel', 'Model');

/**
 * CirclePin Model
 */
class CirclePin extends AppModel
{
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'circle_orders'         => [
            'isString'  => [
                'rule' => ['isString',],
            ],
            'maxLength' => ['rule' => ['maxLength', 4294967295]],
        ],
    ];

    /**
     * @param int $userId
     * @param int $teamId
     *
     * @return array|null
     */
    public function getUnique(int $userId, int $teamId)
    {
        $options = [
            'conditions' => [
                'CirclePin.user_id' => $userId,
                'CirclePin.team_id' => $teamId,
                'CirclePin.del_flgl' => false,
            ],
        ];
        $res = $this->find('first', $options);

        if (empty($res)) {
            return [];
        }
        return Hash::get($res, 'CirclePin');
    }
}
