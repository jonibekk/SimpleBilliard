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
     * Check whether argument user saved item each target post
     *
     * @param array $postIds
     * @param int   $userId
     *
     * @return array
     */
    public function isSavedEachPost(array $postIds, int $userId): array
    {
        if (empty($postIds)) {
            return [];
        }

        $options = [
            'fields'     => 'post_id',
            'conditions' => [
                'post_id' => $postIds,
                'user_id' => $userId,
            ],
        ];
        $res = $this->find('all', $options);

        $default = array_fill_keys($postIds, false);
        if (empty($res)) {
            return $default;
        }

        $res = array_fill_keys(Hash::extract($res, '{n}.SavedPost.post_id'), true);
        return $res + $default;
    }

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
