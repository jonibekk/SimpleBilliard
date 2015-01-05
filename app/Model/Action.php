<?php
App::uses('AppModel', 'Model');
App::uses('ActionResult', 'Model');

/**
 * Action Model
 *
 * @property Team         $Team
 * @property Goal         $Goal
 * @property KeyResult    $KeyResult
 * @property User         $User
 * @property ActionResult $ActionResult
 */
class Action extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    public $actsAs = [
        'Upload' => [
            'photo1' => [
                'styles'  => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo2' => [
                'styles'  => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo3' => [
                'styles'  => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo4' => [
                'styles'  => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
            'photo5' => [
                'styles'  => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 100,
            ],
        ],
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'photo1'      => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo2'      => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo3'      => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo4'      => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo5'      => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'priority'    => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'repeat_type' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'mon_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'tues_flg'    => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'wed_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'thurs_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'fri_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'sat_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'sun_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'     => [
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
        'Goal'      => [
            "counterCache" => true,
            'counterScope' => ['Action.del_flg' => false]
        ],
        'KeyResult' => [
            "counterCache" => true,
            'counterScope' => ['Action.del_flg' => false]
        ],
        'User',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'ActionResult',
    ];

    public function addCompletedAction($data, $goal_id)
    {
        if (!isset($data['Action'])) {
            return false;
        }

        $data['Action']['team_id'] = $this->current_team_id;
        $data['Action']['goal_id'] = $goal_id;
        $data['Action']['user_id'] = $this->my_uid;
        $data['ActionResult'][0]['team_id'] = $this->current_team_id;
        $data['ActionResult'][0]['created_user_id'] = $this->my_uid;
        $data['ActionResult'][0]['completed_user_id'] = $this->my_uid;
        if (isset($data['Action']['key_result_id'])) {
            $data['ActionResult'][0]['type'] = ActionResult::TYPE_KR;
        }
        else {
            $data['ActionResult'][0]['type'] = ActionResult::TYPE_GOAL;
        }
        $data['ActionResult'][0]['completed'] = time();
        $data['ActionResult'][0]['completed_flg'] = true;
        $res = $this->saveAll($data);
        return $res;
    }

    public function releaseKr($kr_id)
    {
        $res = $this->updateAll(['Action.key_result_id' => null],
                                ['Action.key_result_id' => $kr_id, 'Action.team_id' => $this->current_team_id]);
        return $res;
    }

    public function releaseGoal($goal_id)
    {
        $res = $this->updateAll(['Action.goal_id' => null, 'Action.key_result_id' => null],
                                ['Action.goal_id' => $goal_id, 'Action.team_id' => $this->current_team_id]);
        return $res;
    }

}
