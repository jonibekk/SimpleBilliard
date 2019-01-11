<?php
App::import('Service', 'AppService');
App::import('Model/Entity', 'PostFileEntity');
App::uses('PostFile', 'Model');

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
}