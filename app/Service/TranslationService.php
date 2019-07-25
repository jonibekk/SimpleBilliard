<?php
App::import('Service', 'AppService');
App::import('Service', 'ActionService');
App::import('Service', 'CommentService');
App::import('Service', 'PostService');
App::import('Service', 'TeamTranslationStatusService');
App::uses('ActionResult', 'Model');
App::uses('Comment', 'Model');
App::uses('Post', 'Model');
App::uses('TeamTranslationLanguag', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('Translation', 'Model');
App::import('Lib/Translation', 'TranslationResult');
App::import('Lib/Translation', 'GoogleTranslatorClient');
App::uses('MentionComponent', 'Controller/Component');

use Goalous\Enum\Language as LanguageEnum;
use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Enum\Model\Translation\Status as TranslationStatus;
use Goalous\Exception as GlException;

class TranslationService extends AppService
{
    const MAX_TRY_COUNT = 8;
    const RETRY_SLEEP_SECS = 2;

    /**
     *  Get translation for a data
     *
     * @param TranslationContentType $contentType Content type.
     * @param int                    $contentId   Id of content type.
     *                                            ACTION_POST => posts.id
     *                                            CIRCLE_POST => posts.id
     *                                            CIRCLE_POST_COMMENT => comments.id
     *                                            ACTION_POST_COMMENT => comments.id
     * @param string                 $targetLanguage
     *
     * @return TranslationResult
     * @throws Exception
     */
    public function getTranslation(TranslationContentType $contentType, int $contentId, string $targetLanguage): TranslationResult
    {
        if (!LanguageEnum::isValid($targetLanguage)) {
            throw new InvalidArgumentException("Invalid language code: $targetLanguage");
        }

        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        if ($Translation->hasTranslation($contentType, $contentId, $targetLanguage)) {


            $tryCount = 0;

            do {
                $translation = $Translation->getTranslation($contentType, $contentId, $targetLanguage);

                $sourceModel = $this->getSourceModel($contentType, $contentId);

                if ($translation['status'] === TranslationStatus::DONE) {
                    return new TranslationResult($sourceModel['language'], $translation['body'], $targetLanguage);
                }

                sleep(self::RETRY_SLEEP_SECS);
                $tryCount++;
            } while ($tryCount < self::MAX_TRY_COUNT);

            $this->eraseTranslation($contentType, $contentId, $targetLanguage);
        }

        $this->createTranslation($contentType, $contentId, $targetLanguage);

        $sourceModel = $this->getSourceModel($contentType, $contentId);

        $translation = $Translation->getTranslation($contentType, $contentId, $targetLanguage);

        return new TranslationResult($sourceModel['language'], $translation['body'], $targetLanguage);
    }

    /**
     * Delete existing entry
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId Id of content type.
     *                                          ACTION_POST => posts.id
     *                                          CIRCLE_POST => posts.id
     *                                          CIRCLE_POST_COMMENT => comments.id
     *                                          ACTION_POST_COMMENT => comments.id
     * @param string                 $targetLanguage
     *
     * @throws Exception
     */
    public function eraseTranslation(TranslationContentType $contentType, int $contentId, string $targetLanguage)
    {
        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        try {
            $this->TransactionManager->begin();
            $Translation->eraseTranslation($contentType, $contentId, $targetLanguage);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Failed to erase translation.', [
                'message'      => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
                'content_type' => $contentType->getValue(),
                'content_id'   => $contentId,
                'language'     => $targetLanguage
            ]);
            throw $e;
        }
    }

    /**
     * Create translation for a data
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId Id of content type.
     *                                          ACTION_POST => posts.id
     *                                          CIRCLE_POST => posts.id
     *                                          CIRCLE_POST_COMMENT => comments.id
     *                                          ACTION_POST_COMMENT => comments.id
     * @param string                 $targetLanguage
     *
     * @throws Exception
     */
    public function createTranslation(TranslationContentType $contentType, int $contentId, string $targetLanguage)
    {
        if (!LanguageEnum::isValid($targetLanguage)) {
            throw new InvalidArgumentException("Invalid language code: $targetLanguage");
        }

        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        try {
            $this->TransactionManager->begin();
            $Translation->createEntry($contentType, $contentId, $targetLanguage);
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Failed to create translation entry.', [
                'message'      => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
                'content_type' => $contentType->getValue(),
                'content_id'   => $contentId,
                'language'     => $targetLanguage
            ]);
            throw $e;
        }

        $sourceModel = $this->getSourceModel($contentType, $contentId);
        $sourceBody = $sourceModel['body'];
        $teamId = $sourceModel['team_id'];

        /** @var TeamTranslationStatusService $TeamTranslationStatusService */
        $TeamTranslationStatusService = ClassRegistry::init('TeamTranslationStatusService');

        $TranslatorClient = $this->getTranslatorClient();

        try {
            $this->TransactionManager->begin();
            $translatedResult = $TranslatorClient->translate($sourceBody, $targetLanguage);
            $this->updateSourceBodyLanguage($contentType, $contentId, $translatedResult->getSourceLanguage());

            $Translation->updateTranslationBody($contentType, $contentId, $targetLanguage, $translatedResult->getTranslation());

            $TeamTranslationStatusService->incrementUsageCount($teamId, $contentType, StringUtil::mbStrLength($sourceBody));
            $this->TransactionManager->commit();
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            GoalousLog::error('Failed to insert translation.', [
                'message'      => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
                'content_type' => $contentType->getValue(),
                'content_id'   => $contentId,
                'language'     => $targetLanguage
            ]);
            throw $e;
        }
    }

    /**
     * Create default translation of a content
     *
     * @param int                    $teamId
     * @param TranslationContentType $contentType
     * @param int                    $contentId Id of content type.
     *                                          ACTION_POST => posts.id
     *                                          CIRCLE_POST => posts.id
     *                                          CIRCLE_POST_COMMENT => comments.id
     *                                          ACTION_POST_COMMENT => comments.id
     *
     * @throws Exception
     */
    public function createDefaultTranslation(int $teamId, TranslationContentType $contentType, int $contentId)
    {
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
                $TranslationService->createTranslation($contentType, $contentId, $defaultLanguage);
            } catch (Exception $e) {
                GoalousLog::error('Failed create default translation on new content', [
                    'message'      => $e->getMessage(),
                    'trace'        => $e->getTraceAsString(),
                    'content_type' => $contentType->getKey(),
                    'content_id'   => $contentId,
                ]);
            }
        }
    }

    /**
     * Update the detected language
     *
     * @param int                    $userId
     * @param TranslationContentType $contentType
     * @param int                    $contentId Id of content type.
     *                                          ACTION_POST => posts.id
     *                                          CIRCLE_POST => posts.id
     *                                          CIRCLE_POST_COMMENT => comments.id
     *                                          ACTION_POST_COMMENT => comments.id
     *
     * @return bool
     */
    public function checkUserAccess(int $userId, TranslationContentType $contentType, int $contentId): bool
    {
        switch ($contentType->getValue()) {
            case TranslationContentType::ACTION_POST:
                /** @var ActionService $ActionService */
                $ActionService = ClassRegistry::init('ActionService');
                return $ActionService = $ActionService->checkUserAccess($userId, $contentId);
            case TranslationContentType::CIRCLE_POST:
                /** @var PostService $PostService */
                $PostService = ClassRegistry::init('PostService');
                return $PostService->checkUserAccessToCirclePost($userId, $contentId);
                break;
            case TranslationContentType::CIRCLE_POST_COMMENT:
            case TranslationContentType::ACTION_POST_COMMENT:
                /** @var CommentService $CommentService */
                $CommentService = ClassRegistry::init('CommentService');
                return $CommentService->checkUserAccessToComment($userId, $contentId);
                break;
        }

        return false;
    }

    /**
     * Get source body of data to be translated
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId
     *
     * @return array
     *                 ['body' => "", 'language' => "", 'team_id' => 0]
     */
    private function getSourceModel(TranslationContentType $contentType, int $contentId): array
    {
        $originalModel = [];

        switch ($contentType->getValue()) {
            case TranslationContentType::ACTION_POST:
                /** @var Post $Post */
                $Post = ClassRegistry::init('Post');
                $post = $Post->getById($contentId);
                /** @var ActionResult $ActionResult */
                $ActionResult = ClassRegistry::init('ActionResult');
                $actionResult = $ActionResult->getById($post['action_result_id']);
                if (empty($actionResult)) {
                    break;
                }
                $originalModel = [
                    'body'     => $actionResult['name'],
                    'language' => $post['language'] ?: "",
                    'team_id'  => $actionResult['team_id']
                ];
                break;
            case TranslationContentType::CIRCLE_POST:
                /** @var Post $Post */
                $Post = ClassRegistry::init('Post');
                $post = $Post->getById($contentId);
                if (empty($post)) {
                    break;
                }
                $originalModel = [
                    'body'     => $post['body'],
                    'language' => $post['language'] ?: "",
                    'team_id'  => $post['team_id']
                ];
                break;
            case TranslationContentType::CIRCLE_POST_COMMENT:
            case TranslationContentType::ACTION_POST_COMMENT:
                /** @var Comment $Comment */
                $Comment = ClassRegistry::init('Comment');
                $comment = $Comment->getById($contentId);
                if (empty($comment)) {
                    break;
                }
                $commentBody = MentionComponent::replaceMentionForTranslation($comment['body']);
                $originalModel = [
                    'body'     => $commentBody,
                    'language' => $comment['language'] ?: "",
                    'team_id'  => $comment['team_id']
                ];
                break;
        }

        if (empty($originalModel)) {
            throw new GlException\GoalousNotFoundException('Original body for translation is not found');
        }

        return $originalModel;
    }

    /**
     * Update the detected language
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId
     * @param string                 $sourceLanguage
     *
     * @throws Exception
     */
    private function updateSourceBodyLanguage(TranslationContentType $contentType, int $contentId, string $sourceLanguage)
    {
        switch ($contentType->getValue()) {
            case TranslationContentType::ACTION_POST:
            case TranslationContentType::CIRCLE_POST:
                /** @var Post $Post */
                $Post = ClassRegistry::init('Post');
                $Post->updateLanguage($contentId, $sourceLanguage);
                break;
            case TranslationContentType::CIRCLE_POST_COMMENT:
            case TranslationContentType::ACTION_POST_COMMENT:
                /** @var Comment $Comment */
                $Comment = ClassRegistry::init('Comment');
                $Comment->updateLanguage($contentId, $sourceLanguage);
                break;
        }
    }

    /**
     * Get singleton of GoogleTranslatorClient
     *
     * @return GoogleTranslatorClient
     */
    private function getTranslatorClient(): GoogleTranslatorClient
    {
        $registeredClient = ClassRegistry::getObject(GoogleTranslatorClient::class);
        if ($registeredClient instanceof GoogleTranslatorClient) {
            return $registeredClient;
        }
        return new GoogleTranslatorClient();
    }
}