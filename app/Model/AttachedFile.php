<?php
App::uses('AppModel', 'Model');

/**
 * AttachedFile Model
 *
 * @property User        $User
 * @property Team        $Team
 * @property CommentFile $CommentFile
 * @property PostFile    $PostFile
 */
class AttachedFile extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'file_type'  => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'file_size'  => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'model_type' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'    => [
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
        'User',
        'Team',
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'CommentFile',
        'PostFile',
    );

}
