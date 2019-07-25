<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'TeamMemberService');
App::import('Service', 'TeamTranslationLanguageService');
App::import('Service', 'TranslationService');
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');

use Goalous\Enum\Language as LangEnum;
use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Exception\GoalousNotFoundException;

class TranslationsController extends ApiController
{
    use  TranslationNotificationTrait;

    public function get_list()
    {
        $error = $this->validateQuery();

        if (!empty($error)) {
            return $this->_getResponseValidationFail($error);
        }

        $contentTypeValue = $this->request->query('type');
        $contentId = $this->request->query('id');
        $language = $this->request->query('lang');
        $teamId = $this->current_team_id;

        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        if (($Team->isFreeTrial($teamId) || $Team->isPaidPlan($teamId)) && (!$TeamTranslationLanguage->canTranslate($teamId)
                || $TeamTranslationStatus->getUsageStatus($teamId)->isLimitReached())) {
            return $this->_getResponseBadFail("Team can't translate");
        }

        try {
            if (empty($language)) {
                /** @var TeamMemberService $TeamMemberService */
                $TeamMemberService = ClassRegistry::init('TeamMemberService');

                $userId = $this->Auth->user('id');
                $teamId = $this->current_team_id;
                $browserLanguages = CakeRequest::acceptLanguage();

                $language = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, $userId, $browserLanguages);
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

        $this->sendTranslationUsageNotification($teamId);

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

        if (!empty($language)) {
            if (LangEnum::isValid($language)) {
                /** @var TeamTranslationLanguage $TeamTranslationLanguage */
                $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
                if (!$TeamTranslationLanguage->supportTranslationLanguage($teamId, $language)) {
                    $errorMessage[] = "Language not supported in team";
                }
            } else {
                $errorMessage[] = "Invalid language code.";
            }
        }

        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        if (!$TranslationService->checkUserAccess($userId, TranslationContentType::getEnumObj($contentType), $contentId)) {
            $errorMessage[] = "User has no access";
        }

        return $errorMessage;
    }
}