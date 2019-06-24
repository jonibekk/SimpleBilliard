<?php
App::import('Service', 'AppService');
App::uses('TeamTranslationStatus', 'Model');
App::uses('Translation', 'Model');
App::import('Lib/Translation', 'TranslationResult');
App::import('Lib/Translation', 'GoogleTranslatorClient');

use Goalous\Enum\Language as LanguageEnum;
use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Enum\Model\Translation\TranslationStatus as TranslationStatus;
use Goalous\Exception as GlException;

class TranslationService extends AppService
{
    const MAX_TRY_COUNT = 8;
    const RETRY_SLEEP_SECS = 2;

    /**
     *  Get translation for a data
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId
     * @param string                 $targetLanguage
     *
     * @return TranslationResult
     * @throws Exception
     */
    public function getTranslation(TranslationContentType $contentType, int $contentId, string $targetLanguage): TranslationResult
    {
        if (!LanguageEnum::isValid($targetLanguage)) {
            throw new InvalidArgumentException('Invalid language code');
        }

        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');

        if ($Translation->hasTranslation($contentType, $contentId, $targetLanguage)) {

            $sourceModel = $this->getSourceModel($contentType, $contentId);

            $tryCount = 0;

            do {
                $translation = $Translation->getTranslation($contentType, $contentId, $targetLanguage);

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
     * @param int                    $contentId
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
     * @param int                    $contentId
     * @param string                 $targetLanguage
     *
     * @throws Exception
     */
    public function createTranslation(TranslationContentType $contentType, int $contentId, string $targetLanguage)
    {
        if (!LanguageEnum::isValid($targetLanguage)) {
            throw new InvalidArgumentException('Invalid language code');
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
        $teamId =$sourceModel['team_id'];

        $TranslatorClient = $this->getTranslatorClient();

        $translatedResult = $TranslatorClient->translate($sourceBody, $targetLanguage);

        try {
            /** @var TeamTranslationStatusService $TeamTranslationStatusService */
            $TeamTranslationStatusService = ClassRegistry::init('TeamTranslationStatusService');

            $this->TransactionManager->begin();
            $this->updateSourceBodyLanguage($contentType, $contentId, $translatedResult->getSourceLanguage());
            $Translation->updateTranslationBody($contentType, $contentId, $targetLanguage, $translatedResult->getTranslation());
            $TeamTranslationStatusService->incrementUsageCount($teamId, $contentType, mb_strlen($sourceBody, 'UTF-8'));
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
     * Get source body of data to be translated
     *
     * @param TranslationContentType $contentType
     * @param int                    $contentId
     *
     * @return BaseEntity
     */
    private function getSourceModel(TranslationContentType $contentType, int $contentId): BaseEntity
    {
        $originalModel = [];

        switch ($contentType->getValue()) {
            case TranslationContentType::ACTION_POST:
            case TranslationContentType::CIRCLE_POST:
                App::uses('Post', 'Model');
                /** @var Post $Post */
                $Post = ClassRegistry::init('Post');
                $originalModel = $Post->getEntity($contentId);
                break;
            case TranslationContentType::CIRCLE_POST_COMMENT:
            case TranslationContentType::ACTION_POST_COMMENT:
                App::uses('Comment', 'Model');
                /** @var Comment $Comment */
                $Comment = ClassRegistry::init('Comment');
                $originalModel = $Comment->getEntity($contentId);
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
                App::uses('Post', 'Model');
                /** @var Post $Post */
                $Post = ClassRegistry::init('Post');
                $Post->updateLanguage($contentId, $sourceLanguage);
                break;
            case TranslationContentType::CIRCLE_POST_COMMENT:
            case TranslationContentType::ACTION_POST_COMMENT:
                App::uses('Comment', 'Model');
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