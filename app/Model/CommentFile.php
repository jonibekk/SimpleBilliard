<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'CommentFileEntity');

/**
 * CommentFile Model
 *
 * @property Comment      $Comment
 * @property AttachedFile $AttachedFile
 * @property Team         $Team
 */

use Goalous\Enum\DataType\DataType as DataType;

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

    public $modelConversionTable = [
        'comment_id'       => DataType::INT,
        'attached_file_id' => DataType::INT,
        'team_id'          => DataType::INT,
        'index_num'        => DataType::INT
    ];

    /**
     * Get all comment files of a comment
     *
     * @param int $commentId
     *
     * @return CommentFileEntity[]
     */
    public function getAllCommentFiles(int $commentId): array
    {
        $condition = [
            'conditions' => [
                'comment_id' => $commentId,
                'del_flg'    => false
            ]
        ];

        return $this->useType()->useEntity()->find('all', $condition);
    }
}
