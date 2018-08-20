<?php
App::uses('AppModel', 'Model');

/**
 * PostFile Model
 *
 * @property Post         $Post
 * @property AttachedFile $AttachedFile
 * @property Team         $Team
 */

use Goalous\Enum\DataType\DataType as DataType;

class PostFile extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
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
    public $belongsTo = array(
        'Post',
        'AttachedFile',
        'Team',
    );

    protected $modelConversionTable = [
        'post_id'          => DataType::INT,
        'attached_file_id' => DataType::INT,
        'team_id'          => DataType::INT,
        'index'            => DataType::INT
    ];
}
