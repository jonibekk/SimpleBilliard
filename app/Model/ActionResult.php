<?php
App::uses('AppModel', 'Model');

/**
 * ActionResult Model
 *
 * @property Team   $Team
 * @property Action $Action
 * @property User   $CreatedUser
 * @property User   $CompletedUser
 */
class ActionResult extends AppModel
{
    /**
     * タイプ
     */
    const TYPE_USER = 0;
    const TYPE_GOAL = 1;
    const TYPE_KR = 2;

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
        'photo1'  => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo2'  => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo3'  => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo4'  => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo5'  => [
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
