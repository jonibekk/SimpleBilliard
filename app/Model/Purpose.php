<?php
App::uses('AppModel', 'Model');

/**
 * Purpose Model
 *
 * @property User $User
 * @property Team $Team
 * @property Goal $Goal
 */
class Purpose extends AppModel
{

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'    => [
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
        'User',
        'Team',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Goal',
    ];

    function add($data)
    {
        if (!isset($data['Purpose']) || empty($data['Purpose'])) {
            return false;
        }
        Cache::delete($this->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        $data['Purpose']['team_id'] = $this->current_team_id;
        $data['Purpose']['user_id'] = $this->my_uid;
        return $this->save($data);
    }

    function getPurposesNoGoal($uid = null)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        $options = [
            'conditions' => [
                'team_id'    => $this->current_team_id,
                'user_id'    => $uid,
                'goal_count' => 0,
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    function getMyPurposeCount($user_id = null, $start_date = null, $end_date = null)
    {
        $user_id = $user_id ? $user_id : $this->my_uid;
        $start_date = $start_date ? $start_date : $this->Team->EvaluateTerm->getCurrentTermData()['start_date'];
        $end_date = $end_date ? $end_date : $this->Team->EvaluateTerm->getCurrentTermData()['end_date'];
        $options = [
            'conditions' => [
                'user_id'    => $user_id,
                'created >=' => $start_date,
                'created <=' => $end_date,
            ]
        ];
        $res = $this->find('count', $options);
        return $res;
    }

}
