<?php
App::uses('AppModel', 'Model');

/**
 * CircleMember Model
 *
 * @property Circle $Circle
 * @property Team   $Team
 * @property User   $User
 */
class CircleMember extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'admin_flg' => [
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
        'Circle',
        'Team',
        'User',
    ];

    public function getMyCircleList()
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
                'user_id' => $this->me['id'],
            ],
            'fields'     => ['circle_id'],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

}
