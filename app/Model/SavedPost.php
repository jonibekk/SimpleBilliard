<?php
App::uses('AppModel', 'Model');

/**
 * SavedPost Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class SavedPost extends AppModel
{
    public $actsAs = [
        'SoftDeletable' => [
            'delete' => false,
        ],
    ];

    /**
     * @param int $postId
     * @param int $userId
     *
     * @return array|null
     */
    public function getUnique(int $postId, int $userId)
    {
        $options = [
            'conditions' => [
                'post_id' => $postId,
                'user_id' => $userId,
            ],
        ];
        $res = $this->find('first', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::get($res, 'SavedPost');
    }
}
