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
     * @param int $index
     *
     * @return PostFileEntity
     * @throws Exception
     */
    private function add(int $postId, int $attachedFileId, int $teamId, int $index): PostFileEntity
    {
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');

        $newData = [
            'post_id'          => $postId,
            'attached_file_id' => $attachedFileId,
            'team_id'          => $teamId,
            'index_num'        => $index
        ];

        return $PostFile->useType()->useEntity()->save($newData, false);
    }
}