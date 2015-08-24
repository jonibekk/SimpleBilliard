<?php
App::uses('AppModel', 'Model');

/**
 * CommentFile Model
 *
 * @property Comment      $Comment
 * @property AttachedFile $AttachedFile
 * @property Team         $Team
 */
class CommentFile extends AppModel
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
        'Comment',
        'AttachedFile',
        'Team',
    );
}
