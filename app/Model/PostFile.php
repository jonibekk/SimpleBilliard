<?php
App::uses('AppModel', 'Model');

/**
 * PostFile Model
 *
 * @property Post         $Post
 * @property AttachedFile $AttachedFile
 * @property Team         $Team
 */
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
}
