<?php
App::import('Service', 'AppService');
App::import('Service', 'PostService');
App::import('Service', 'CommentFileService');
App::import('Service', 'AttachedFileService');
App::import('Service', 'UploadService');
App::import('Service', 'TranslationService');
App::import('Lib/Storage', 'UploadedFile');
App::uses('Comment', 'Model');
App::uses('CommentFile', 'Model');
App::uses('CommentLike', 'Model');
App::uses('CommentRead', 'Model');
App::uses('CommentMention', 'Model');
App::uses('Post', 'Model');
App::uses('Translation', 'Model');
App::uses('PostShareCircle', 'Model');
App::import('Model/Entity', 'CommentEntity');
App::import('Model/Entity', 'CommentFileEntity');
App::import('Model/Entity', 'AttachedFileEntity');


/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/17
 * Time: 16:50
 */

use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Exception as GlException;
use Goalous\Enum\Model\AttachedFile\AttachedFileType as AttachedFileType;
use Goalous\Enum\Model\AttachedFile\AttachedModelType as AttachedModelType;

class CommentService extends AppService
{

    /**
     * Check whether user has access to the post where the comment belongs in
     *
     * @param int $userId
     * @param int $commentId
     *
     * @return bool
     */
    public function checkUserAccessToComment(int $userId, int $commentId): bool
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $options = [
            'conditions' => [
                'id' => $commentId
            ],
            'fields'     => [
                'post_id'
            ]
        ];

        $comments = $Comment->useType()->find('first', $options);

        if (empty($comments)) {
            throw new GlException\GoalousNotFoundException(__("This comment doesn't exist."));
        }

        /** @var int $postId */
        $postId = Hash::extract($comments, '{s}.post_id')[0];

        if (empty($postId)) {
            throw new GlException\GoalousNotFoundException(__("This post doesn't exist."));
        }

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        switch ($Post->getPostType($postId)) {
            case Post::TYPE_NORMAL:
                /** @var PostService $PostService */
                $PostService = ClassRegistry::init('PostService');
                return $PostService->checkUserAccessToCirclePost($userId, $postId);
                break;
            case Post::TYPE_ACTION:
                /** @var ActionService $ActionService */
                $ActionService = ClassRegistry::init('ActionService');
                return $ActionService = $ActionService->checkUserAccess($userId, $postId);
                break;
            default:
                return false;
        }
    }

    /**
     * Check whether the user can view the several comments
     *
     * @param int $userId
     * @param int $commentsIds
     *
     * @throws Exception
     */
    public function checkUserAccessToMultipleComment(int $userId, array $commentsIds)
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        $options = [
            'conditions' => [
                'id' => $commentsIds
            ],
            'fields'     => [
                'post_id'
            ]
        ];

        $comments = $Comment->useType()->find('first', $options);

        if (empty($comments)) {
            throw new GlException\GoalousNotFoundException(__("This comment doesn't exist."));
        }

        /** @var int $postId */
        $postsIds = Hash::extract($comments, '{s}.post_id');

        if (empty($postsIds)) {
            throw new GlException\GoalousNotFoundException(__("This post doesn't exist."));
        }

        return $PostService->checkUserAccessToMultiplePost($userId, $postsIds);
    }

    /**
     * Method to save a comment
     *
     * @param array    $commentBody
     * @param int      $postId
     * @param int      $userId
     * @param int      $teamId
     * @param string[] $fileIDs
     *
     * @return CommentEntity of saved comment
     * @throws Exception
     */
    public function add(
        array $commentBody,
        int $postId,
        int $userId,
        int $teamId,
        array $fileIDs = []
    ): CommentEntity
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');

        if (!$Post->exists($postId)) {
            throw new GlException\GoalousNotFoundException(__("This post doesn't exist."));
        }

        try {
            $this->TransactionManager->begin();
            $Comment->create();

            $commentBody['post_id'] = $postId;
            $commentBody['user_id'] = $userId;
            $commentBody['team_id'] = $teamId;
            $commentBody['created'] = GoalousDateTime::now()->getTimestamp();
            // OGP
            $commentBody['site_info'] = !empty($commentBody['site_info']) ? json_encode($commentBody['site_info']) : null;

            /** @var CommentEntity $savedComment */
            $savedComment = $Comment->useType()->useEntity()->save($commentBody, false);

            if (empty($savedComment)) {
                GoalousLog::error('Error on adding comment: failed comment save', [
                    'commentData' => $commentBody
                ]);
                throw new RuntimeException('Error on adding post: failed comment save');
            }

            $commentId = $savedComment['id'];

            $newCommentCount = $Comment->getCommentCount($postId);

            if (!$Post->updateCommentCount($postId, $newCommentCount)) {
                GoalousLog::error('Error on adding comment: failed updating posts.comment_count', [
                    'commentData' => $commentBody
                ]);
                throw new RuntimeException('Error on adding post: failed updating posts.comment_count');

            }

            //Saved attached files
            if (!empty($fileIDs)) {
                //try to get the circle
                $circles = $PostShareCircle->getShareCircleList( $postId, $teamId );
                $circleId = ( count( $circles ) > 0 ) ? $circles[0] : null; //before there could be multiple circles per post, now it is only one

                //save files
                $this->saveFiles($commentId, $userId, $teamId, $fileIDs, 0, $circleId, $postId);
            }

            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            throw $e;
        }

        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        if ($TranslationService->canTranslate($teamId)) {
            // Create default translation
            switch ($Post->getPostType($postId)) {
                case Post::TYPE_NORMAL:
                    $TranslationService->createDefaultTranslation($teamId, TranslationContentType::CIRCLE_POST_COMMENT(), $commentId);
                    break;
                case Post::TYPE_ACTION:
                    $TranslationService->createDefaultTranslation($teamId, TranslationContentType::ACTION_POST_COMMENT(), $commentId);
                    break;
            }
        }

        return $savedComment;
    }

    /**
     * Delete comment
     *
     * @param commentId
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(int $commentId): bool
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        /** @var CommentFileService $CommentFileService */
        $CommentFileService = ClassRegistry::init('CommentFileService');
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        //Check if comment exists & not deleted
        $commentCondition = [
            'conditions' => [
                'id'      => $commentId,
                'del_flg' => false
            ], 'fields'  => [
                'post_id'
            ]
        ];

        $postId = $Comment->useType()->find('first', $commentCondition);
        $postId = Hash::get($postId, "Comment.post_id");

        if (!$postId) {
            throw new GlException\GoalousNotFoundException(__("This comment doesn't exist."));
        }

        $modelsToDelete = [
            'CommentLike'    => 'comment_id',
            'CommentRead'    => 'comment_id',
            'CommentMention' => 'post_id',
            'Comment'        => 'Comment.id'
        ];

        try {
            $this->TransactionManager->begin();
            foreach ($modelsToDelete as $model => $column) {
                /** @var AppModel $Model */
                $Model = ClassRegistry::init($model);

                $condition = [$column => $commentId];

                $res = $Model->softDeleteAll($condition, false);
                if (!$res) {
                    throw new RuntimeException("Error on deleting ${model} for comment $commentId: failed comment soft delete");
                }
            }
            $CommentFileService->softDeleteAllFiles($commentId);

            //Countdown the post comments number
            $newCommentCount = $Comment->getCommentCount($postId);
            if (!$Post->updateCommentCount($postId, $newCommentCount)) {
                GoalousLog::error('Error on deleting comment: failed updating posts.comment_count');
                throw new RuntimeException('Error on deleting post: failed updating post comment_count');
            }

            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Error on deleting comment $commentId: failed comment soft delete", $e->getTrace());
            throw $e;
        }

        $this->deleteAllTranslations($commentId);

        return true;
    }

    /**
     * Edit a comment body
     *
     * @param CommentUpdateRequest $data
     *
     * @return CommentEntity Updated comment
     * @throws Exception
     */
    public function edit(CommentUpdateRequest $data): CommentEntity
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        if (!$Comment->exists($data->getId())) {
            throw new GlException\GoalousNotFoundException(__("This comment doesn't exist."));
        }

        try {
            $this->TransactionManager->begin();

            $newData = [
                'body'      => $data->getBody(),
                'site_info' => !empty($data->getSiteInfo()) ? json_encode($data->getSiteInfo()) : null,
                'created'   => false // Prevent updating this field
            ];

            $Comment->clear();
            $commentId = $data->getId();
            $Comment->id = $commentId;
            if (!$Comment->save($newData, false)) {
                throw new RuntimeException(sprintf("Failed to update comments table record. data: %s",
                    AppUtil::jsonOneLine(compact('newData', 'commentId'))
                ));
            }
            $this->updateAttachedFiles($data->getId(), $data->getUserId(), $data->getTeamId(), $data->getResources());

            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error("Failed to update comment", [
                'message' => $e->getMessage(),
                'data'    => $data,
                'trace'   => $e->getTraceAsString()
            ]);
            throw $e;
        }

        $this->deleteAllTranslations($commentId);

        /** @var CommentEntity $result */
        $result = $Comment->useType()->useEntity()->find('first', ['conditions' => ['id' => $data->getId()]]);
        return $result;
    }

    /**
     * Update attached files
     * - delete only files user deleted existing on comment form
     * - save new files
     *
     * @param int   $commentId
     * @param int   $userId
     * @param int   $teamId
     * @param array $resources Existing resources
     *
     * @return PostResource[]
     * @throws Exception
     */
    private function updateAttachedFiles(int $commentId, int $userId, int $teamId, array $resources)
    {
        /** @var CommentFile $CommentFile */
        $CommentFile = ClassRegistry::init('CommentFile');
        $oldFiles = $CommentFile->getAllCommentFiles($commentId);
        if (empty($resources) && empty($oldFiles)) {
            return;
        }
        // Each array element is CommentFile entity and can't use Hash::extract
        $oldFileIds = [];
        foreach ($oldFiles as $oldFile) {
            $oldFileIds[] = $oldFile['attached_file_id'];
        }

        $existingFileIds = [];
        $newFileUuids = [];
        foreach ($resources as $resource) {
            if (!array_key_exists('id', $resource)) {
                $newFileUuids[] = $resource['file_uuid'];
            } else {
                $existingFileIds[] = $resource['id'];
            }
        }
        $deleteFileIds = array_diff($oldFileIds, $existingFileIds);

        if (!empty($deleteFileIds)) {
            /** @var CommentFileService $CommentFileService */
            $CommentFileService = ClassRegistry::init('CommentFileService');
            $CommentFileService->deleteAllByAttachedFileIds($deleteFileIds);
        }
        if (!empty($newFileUuids)) {
            $existingFilesMaxOrder = $CommentFile->findMaxOrderOfComment($commentId);
            $this->saveFiles($commentId, $userId, $teamId, $newFileUuids, $existingFilesMaxOrder + 1);
        }
    }

    /**
     * Save uploaded files
     *
     * @param int   $commentId
     * @param int   $userId
     * @param int   $teamId
     * @param array $fileIDs
     *
     * @param int   $commentFileIndex
     *
     * @return bool
     * @throws Exception
     */
    private function saveFiles(int $commentId, int $userId, int $teamId, array $fileIDs, int $commentFileIndex = 0, ?int $circleId = null, ?int $postId = null): bool
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');
        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init('AttachedFileService');
        /** @var CommentFileService $CommentFileService */
        $CommentFileService = ClassRegistry::init('CommentFileService');
        /** @var SearchPostFileService $SearchPostFileService */
        $SearchPostFileService = ClassRegistry::init('SearchPostFileService');

        $addedFiles = [];

        try {
            //Save attached files
            foreach ($fileIDs as $id) {

                if (!is_string($id)) {
                    throw new InvalidArgumentException("Buffered file ID must be string.");
                }

                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $UploadService->getBuffer($userId, $teamId, $id);

                /** @var AttachedFileEntity $attachedFile */
                $attachedFile = $AttachedFileService->add($userId, $teamId, $uploadedFile,
                    AttachedModelType::TYPE_MODEL_COMMENT());

                $addedFiles[] = $attachedFile['id'];

                $CommentFileService->add($commentId, $attachedFile['id'], $teamId, $commentFileIndex++);

                if( ( $circleId !== null ) && ( $postId !== null ) ) {
                    $SearchPostFileService->addAttachedFile(
                        $teamId,
                        $userId,
                        $circleId,
                        $postId,
                        $commentId,
                        $attachedFile['id']
                    );
                }

                $UploadService->saveWithProcessing("AttachedFile", $attachedFile['id'], 'attached', $uploadedFile);
            }
        } catch (Exception $e) {
            //If any error happened, remove uploaded file
            foreach ($addedFiles as $id) {
                $UploadService->deleteAsset('AttachedFile', $id);
            }
            throw $e;
        }

        return true;
    }

    /**
     * Get list of attached files of a post
     *
     * @param int                                              $commentId
     * @param Goalous\Enum\Model\AttachedFile\AttachedFileType $type Filtered file type
     *
     * @return AttachedFileEntity[]
     */
    public function getAttachedFiles(int $commentId, AttachedFileType $type = null): array
    {
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');

        $conditions = [
            'conditions' => [],
            'table'      => 'attached_files',
            'alias'      => 'AttachedFile',
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'comment_files',
                    'alias'      => 'CommentFile',
                    'conditions' => [
                        'CommentFile.comment_id' => $commentId,
                        'CommentFile.attached_file_id = AttachedFile.id'
                    ]
                ]
            ]
        ];

        if (!empty($type)) {
            $conditions['conditions']['file_type'] = $type->getValue();
        }

        return $AttachedFile->useType()->useEntity()->find('all', $conditions);
    }


    /**
     * Delete all translations of a comment
     *
     * @param int $commentId
     * @throws Exception
     */
    public function deleteAllTranslations(int $commentId)
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $post = $Post->getByCommentId($commentId);

        $Comment->clearLanguage($commentId);

        switch ($post['Post']['type']) {
            case Post::TYPE_NORMAL:
                $Translation->eraseAllTranslations(TranslationContentType::CIRCLE_POST_COMMENT(), $commentId);
                break;
            case Post::TYPE_ACTION:
                $Translation->eraseAllTranslations(TranslationContentType::ACTION_POST_COMMENT(), $commentId);
                break;
        }
    }
}
