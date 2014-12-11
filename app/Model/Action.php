<?php
App::uses('AppModel', 'Model');

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
            'photo' => [
                'styles'  => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'    => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality' => 70,
            ],
        ],
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'photo'       => [
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

}
