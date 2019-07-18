<?php
App::import('Service', 'AppService');
App::import('Model/Entity', 'PostFileEntity');
App::uses('PostFile', 'Model');
App::uses('AttachedFile', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/01
 * Time: 12:05
 */
class PostFileService extends AppService
{
    /**
     * Add a new post_files entry
     *
     * @param int $postId
     * @param int $attachedFileId
     * @param int $teamId
     * @param int $indexNum
     *
     * @return PostFileEntity
     * @throws Exception
     */
    public function add(int $postId, int $attachedFileId, int $teamId, int $indexNum): PostFileEntity
    {
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');

        $newData = [
            'post_id'          => $postId,
            'attached_file_id' => $attachedFileId,
            'team_id'          => $teamId,
            'index_num'        => $indexNum
        ];

        try {
            $this->TransactionManager->begin();
            $PostFile->create();
            $result = $PostFile->useType()->useEntity()->save($newData, false);
            $this->TransactionManager->commit();
        } catch (Exception $exception) {
            $this->TransactionManager->rollback();
            GoalousLog::error($errorMessage = 'Failed saving post_files', [
                'posts.id'         => $postId,
                'team.id'          => $teamId,
                'attached_file.id' => $attachedFileId,
            ]);
            throw new RuntimeException('Error on adding post_files: ' . $errorMessage);
        }
        return $result;
    }

    public function getPostFilesByPostId(int $postId)
    {
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');

        $condition = [
            'conditions' => [
                'PostFile.post_id' => $postId,
                'PostFile.del_flg' => [0, 1],
            ],
            'fields'     => [
                'PostFile.id',
                'PostFile.post_id',
                'PostFile.attached_file_id',
                'PostFile.team_id',
                'PostFile.index_num',
                'PostFile.del_flg',
                'PostFile.deleted',
                'PostFile.created',
                'PostFile.modified',
                'AttachedFile.id',
                'AttachedFile.user_id',
                'AttachedFile.team_id',
                'AttachedFile.attached_file_name',
                'AttachedFile.file_type',
                'AttachedFile.file_ext',
                'AttachedFile.file_size',
                'AttachedFile.model_type',
                'AttachedFile.display_file_list_flg',
                'AttachedFile.removable_flg',
                'AttachedFile.del_flg',
                'AttachedFile.deleted',
                'AttachedFile.created',
                'AttachedFile.modified',
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'attached_files',
                    'alias'      => 'AttachedFile',
                    'conditions' => [
                        'AttachedFile.id = PostFile.attached_file_id',
                    ],
                ]
            ]
        ];

        $r = $PostFile->find('all', $condition);
        return $r;
    }

    /**
     * Delete attached files of a post
     *
     * @param int[] $attachedFileIds
     *
     * @throws Exception;
     */
    public function deleteByAttachedFileIds(array $attachedFileIds)
    {
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');
        try {
            $this->TransactionManager->begin();
            $result = $AttachedFile->softDeleteAll(['AttachedFile.id' => $attachedFileIds], false) &&
                $PostFile->softDeleteAll(['PostFile.attached_file_id' => $attachedFileIds], false);
            if (!$result) {
                throw new RuntimeException();
            };
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Failed to delete post files & their attached files.',
                ['attached_file_ids' => $attachedFileIds]);
            throw $e;
        }
    }
}
