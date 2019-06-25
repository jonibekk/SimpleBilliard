<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'TeamMemberService');
App::import('Service', 'TranslationService');

use Goalous\Enum\Language as LangEnum;
use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Exception\GoalousNotFoundException;

class TranslationsController extends ApiController
{
    public function get_list()
    {
        $error = $this->validateQuery();

        if (!empty($error)) {
            return $this->_getResponseValidationFail($error);
        }

        $contentTypeValue = $this->request->query('type');
        $contentId = $this->request->query('id');
        $language = $this->request->query('lang');
        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;

        try {
            if (empty($language)) {
                /** @var TeamMemberService $TeamMemberService */
                $TeamMemberService = ClassRegistry::init('TeamMemberService');
                $language = $TeamMemberService->getDefaultTranslationLanguage($teamId, $userId);
            }

            $contentType = TranslationContentType::getEnumObj($contentTypeValue);

            /** @var TranslationService $TranslationService */
            $TranslationService = ClassRegistry::init('TranslationService');
            $translation = $TranslationService->getTranslation($contentType, $contentId, $language);

        } catch (GoalousNotFoundException $e) {
            return $this->_getResponseNotFound($e->getMessage());
        } catch (Exception $e) {
            return $this->_getResponseInternalServerError($e->getMessage());
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return $this->_getResponseSuccess($translation->toArray());
    }

    private function validateQuery(): array
    {
        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;

        if (empty($userId)) {
            return ["No user ID"];
        }

        if (empty($teamId)) {
            return ["No team ID"];
        }

        $errorMessage = [];

        $contentType = $this->request->query('type');
        $contentId = $this->request->query('id');
        $language = $this->request->query('lang');

        if (empty($contentType)) {
            $errorMessage[] = "Missing translation content type.";
        }

        if (empty($contentId)) {
            $errorMessage[] = "Missing translation content id.";
        }

        if (!empty($language) && !LangEnum::isValid($language)) {
            $errorMessage[] = "Invalid language code.";
        }

        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        if (!$TranslationService->checkUserAccess($userId, TranslationContentType::getEnumObj($contentType), $contentId)) {
            $errorMessage[] = "User has no access";
        }

        return $errorMessage;

    }
}