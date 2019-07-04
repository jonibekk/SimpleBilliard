<?php
App::import('Service', 'AppService');
App::import('Controller/Component', 'OgpComponent');
App::import('Service', 'TranslationService');
App::import('Service', 'TeamTranslationLanguageService');
App::uses('Post', 'Model');
App::uses('Comment', 'Model');
App::uses('Translation', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('Translation', 'Model');

use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;

/**
 * Class CommentService
 */
class ApiCommentService extends AppService
{
    /**
     * @param $id
     *
     * @return array|null
     */
    function get($id)
    {
        if (empty($id)) {
            return [];
        }

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init("Comment");

        try {
            $comment = $Comment->getComment($id);
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return [];
        }
        return $comment;
    }

    function create($data)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        $Post->id = Hash::get($data, 'Comment.post_id');

        try {
            $post = $Post->findById($Post->id);

            // OGP 情報を取得する URL が含まれるテキスト
            // フロントの JS でプレビューが正しく取得出来た場合は、site_info_url に URL が含まれている
            // それ以外の場合は body テキスト全体から URL を検出する
            $url_text = Hash::get($data, 'Comment.site_info_url');
            if (!$url_text) {
                $url_text = Hash::get($data, 'Comment.body');
            }

            $ogp = $this->_getOgpIndex($data);
            $data['Comment'] = am($data['Comment'], $ogp);

            // コメントを追加
            if (!$commentId = $Post->Comment->add($data)) {
                if (!empty($this->Post->Comment->validationErrors)) {
                    $error_msg = array_shift($this->Post->Comment->validationErrors);
                    throw new RuntimeException($error_msg[0]);
                }
            }

            $teamId = Hash::get($post, 'Post.team_id');

            // Make translation
            /** @var TeamTranslationLanguage $TeamTranslationLanguage */
            $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
            /** @var TeamTranslationStatus $TeamTranslationStatus */
            $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

            if ($TeamTranslationLanguage->canTranslate($teamId) && !$TeamTranslationStatus->getUsageStatus($teamId)->isLimitReached()) {

                /** @var TeamTranslationLanguageService $TeamTranslationLanguageService */
                $TeamTranslationLanguageService = ClassRegistry::init('TeamTranslationLanguageService');
                /** @var TranslationService $TranslationService */
                $TranslationService = ClassRegistry::init('TranslationService');

                $defaultLanguage = $TeamTranslationLanguageService->getDefaultTranslationLanguageCode($teamId);

                try {
                    switch (Hash::get($post, 'Post.type')) {
                        case Post::TYPE_NORMAL:
                            $TranslationService->createTranslation(TranslationContentType::CIRCLE_POST_COMMENT(), $commentId, $defaultLanguage);
                            break;
                        case Post::TYPE_ACTION:
                            $TranslationService->createTranslation(TranslationContentType::ACTION_POST_COMMENT(), $commentId, $defaultLanguage);
                            break;
                    }
                } catch (Exception $e) {
                    GoalousLog::error('Failed create translation on new post', [
                        'message' => $e->getMessage(),
                        'trace'   => $e->getTraceAsString(),
                        'post.id' => $post['id'],
                    ]);
                }
            }

        } catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return $Post->Comment;
    }

    /**
     * Validates comment data for creation
     * Returns an empty array in case of success
     *
     * @param $data
     *
     * @return array
     */
    function validateCreate($data)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init("Comment");

        $postId = Hash::get($data, 'Comment.post_id');
        $post = $Post->findById($postId);
        if (empty($post)) {
            return ["status_code" => 404, "message" => __("This post was deleted.")];
        }

        $Comment->set($data['Comment']);
        if (!$Comment->validates()) {
            return [
                "status_code"       => 400,
                "validation_errors" => $this->validationExtract($Comment->validationErrors)
            ];
        }
        return [];
    }

    /**
     * @param $id
     *
     * Delete Comment
     *
     * @return bool
     */
    function delete($id)
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init("Comment");
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init("PostFile");

        try {
            // Validate $id
            if (empty($id) || !$Comment->exists($id)) {
                throw new NotFoundException(__("This comment doesn't exist."));
            }
            // Start transaction
            $Comment->begin();
            $Comment->delete($id);
            $PostFile->AttachedFile->deleteAllRelatedFiles($id, AttachedFile::TYPE_MODEL_COMMENT);
            $this->deleteTranslation($id);
            $Comment->updateCounterCache(['post_id' => $id]);
            // Commit
            $Comment->commit();
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * Update comment data
     *
     * @param $id
     * @param $data
     *
     * @return bool
     */
    function update($id, $data)
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init("Comment");
        $Comment->id = $id;

        try {
            // Get ogp
            $ogp = $this->_getOgpIndex($data);
            $data['Comment'] = am($data['Comment'], $ogp);

            // Save comment data
            $Comment->commentEdit($data);

            $this->deleteTranslation($id);
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * Validates the comment data for update
     * Returns an empty array in case of success.
     *
     * @param $comment_id
     * @param $user_id
     * @param $data
     *
     * @return array
     */
    public function validateUpdate($comment_id, $user_id, $data): array
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init("Comment");

        // Check if comment exists
        $comment = $this->get($comment_id);
        if (empty($comment)) {
            return ["status_code" => 400, "message" => __("Not exist")];
        }

        // Is it the user comment?
        if ($user_id != $comment['User']['id']) {
            return ["status_code" => 403, "message" => __("This isn't your comment.")];
        }

        $commentData = Hash::get($data, 'Comment');
        $Comment->set($commentData);
        if (!$Comment->validates()) {
            return [
                "status_code"       => 400,
                "validation_errors" => $this->validationExtract($Comment->validationErrors)
            ];
        }
        return [];
    }

    /**
     * Return formatted data to be used on comments
     * from OGP data received from frontend.
     *
     * @param $requestData
     *
     * @return array
     */
    function _getOgpIndex($requestData)
    {
        $ogpIndex = [];
        $ogp = Hash::get($requestData, 'OGP');

        if (!$ogp || !isset($ogp['title'])) {
            $ogpIndex['site_info'] = null;
            $ogpIndex['site_photo'] = null;
            return $ogpIndex;
        }

        // Do not set if it is a no-image-link.png from our site
        if (isset($ogp['image']) && strpos($ogp['image'], 'no-image-link') !== false) {
            // No need this image
            unset($ogp['image']);
        }

        $ogpIndex['site_info'] = json_encode($ogp);
        if (isset($ogp['image'])) {
            // Check if it is not already a cached image
            if (strpos($ogp['image'], '/upload/comments/') !== 0) {
                // Get the image
                $ext = UploadBehavior::getImgExtensionFromUrl($ogp['image']);
                if (!$ext) {
                    $ogp['image'] = null;
                }
            }
            $ogpIndex['site_photo'] = $ogp['image'];
        }
        return $ogpIndex;
    }

    /**
     * Delete all translations of a comment
     *
     * @param int $commentId
     */
    private function deleteTranslation(int $commentId)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        $post = $Post->getByCommentId($commentId);

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

