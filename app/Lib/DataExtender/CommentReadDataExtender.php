<?php
App::uses("Comment", "Model");
App::uses("CommentRead", "Model");
App::import('Lib/DataExtender', 'DataExtender');

class CommentReadDataExtender extends DataExtender
{
    /** @var int */
    private $userId;

    /**
     * Set user ID for the extender function
     *
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    protected function fetchData(array $keys): array
    {
        if (empty($this->userId)) {
            throw new RuntimeException("Missing user ID");
        }


        $commentIds = $this->filterKeys($keys);

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        /* Deal as comment is read if comment creator is logged in user */
        $options = [
            'conditions' => [
                'id' => $commentIds,
                'user_id' => $this->userId,
            ],
            'fields' => [
                'id'
            ],
        ];
        $comments = $Comment->find('all', $options);
        $createdCommentIds = Hash::extract($comments, '{n}.Comment.id');
        $notCreatedCommentIds = array_diff($commentIds, $createdCommentIds);
        // All comments are created by logged in user, finish processing
        if(empty($notCreatedCommentIds)) {
            return $createdCommentIds;
        }

        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');

        /* Get whether read comments created by other user. */
        $options = [
            'conditions' => [
                'comment_id' => $notCreatedCommentIds,
                'user_id' => $this->userId,
            ],
            'fields' => [
                'comment_id'
            ],
        ];
        $commentReads = $CommentRead->find('all', $options);
        $readCommentIds = Hash::extract($commentReads, "{n}.{s}.comment_id");
        return array_merge($createdCommentIds, $readCommentIds);
    }

    protected function connectData(
        array $parentData,
        string $parentKeyName,
        array $extData,
        string $extDataKey
    ): array
    {
        foreach ($parentData as $key => &$parentElement) {
            if (!is_int($key)) {
                $parentData['is_read'] = in_array(Hash::get($parentData, $parentKeyName), $extData);
                return $parentData;
            }
            $parentElement['is_read'] = in_array(Hash::get($parentElement, $parentKeyName), $extData);
        }
        return $parentData;
    }

}
