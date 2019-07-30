<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');
App::uses('TeamTranslationLanguage', 'Model');
App::import('Service', 'TeamMemberService');
App::import('Service', 'TeamTranslationLanguageService');
App::import('Service', 'TranslationService');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');

use Goalous\Enum\Language as LangEnum;
use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Exception\GoalousNotFoundException;

class TranslationsController extends BaseApiController
{
    use TranslationNotificationTrait;

    public function get_list()
    {
        $error = $this->validateGetTranslation();

        if (!empty($error)) {
            return $error;
        }

        $contentTypeValue = $this->request->query('type');
        $contentId = $this->request->query('id');
        $language = $this->request->query('lang');

        try {
            if (empty($language)) {
                /** @var TeamMemberService $TeamMemberService */
                $TeamMemberService = ClassRegistry::init('TeamMemberService');

                $userId = $this->getUserId();
                $teamId = $this->getTeamId();
                $browserLanguages = CakeRequest::acceptLanguage();

                $language = $TeamMemberService->getDefaultTranslationLanguageCode($teamId, $userId, $browserLanguages);
            }

            $contentType = TranslationContentType::getEnumObj($contentTypeValue);

            /** @var TranslationService $TranslationService */
            $TranslationService = ClassRegistry::init('TranslationService');
            $translation = $TranslationService->getTranslation($contentType, $contentId, $language);

        } catch (GoalousNotFoundException $e) {
            return ErrorResponse::notFound()->withException($e)->getResponse();
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        $this->sendTranslationUsageNotification($this->getTeamId());

        return ApiResponse::ok()->withData($translation->toArray())->getResponse();
    }

    /**
     * @return CakeResponse|null
     */
    private function validateGetTranslation()
    {
        $contentType = $this->request->query('type');
        $contentId = $this->request->query('id');
        $language = $this->request->query('lang');

        if (empty($contentType)) {
            return ErrorResponse::badRequest()->withMessage("Missing translation content type.")->getResponse();
        }

        if (empty($contentId)) {
            return ErrorResponse::badRequest()->withMessage("Missing translation content id.")->getResponse();
        }

        $currentTeamId = $this->getTeamId();

        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');
        if (!$TranslationService->canTranslate($currentTeamId)) {
            return ErrorResponse::badRequest()->withMessage("Team can't translate.")->getResponse();
        }

        if (!empty($language)) {
            if (LangEnum::isValid($language)) {
                /** @var TeamTranslationLanguage $TeamTranslationLanguage */
                $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
                if (!$TeamTranslationLanguage->isLanguageSupported($currentTeamId, $language)) {
                    return ErrorResponse::badRequest()->withMessage("Language not supported in team.")->getResponse();
                }
            } else {
                return ErrorResponse::badRequest()->withMessage("Invalid language code.")->getResponse();
            }
        }

        /** @var TranslationService $TranslationService */
        $TranslationService = ClassRegistry::init('TranslationService');

        if (!$TranslationService->checkUserAccess($this->getUserId(), TranslationContentType::getEnumObj($contentType), $contentId)) {
            return ErrorResponse::notFound()->withMessage("User has no access or content not found.")->getResponse();
        }

        return null;
    }
}