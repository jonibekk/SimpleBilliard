<?php
App::uses('AppModel', 'Model');

/**
 * ActionResult Model
 *
 * @property Team   $Team
 * @property Action $Action
 * @property User   $User
 */
class ActionResult extends AppModel
{
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
        'photo'   => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
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
        'Action'        => [
            "counterCache" => true,
            'counterScope' => ['ActionResult.del_flg' => false]
        ],
        'CreatedUser'   => [
            'className'  => 'User',
            'foreignKey' => 'created_user_id',
        ],
        'CompletedUser' => [
            'className'  => 'User',
            'foreignKey' => 'completed_user_id',
        ],
    ];
}
