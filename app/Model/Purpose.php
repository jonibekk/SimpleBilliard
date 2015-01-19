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

}
