<?php

App::uses('BaseApiController', 'Controller/Api');
App::uses('TeamRequestValidator', 'Validator/Request/Api/V2');
App::uses('TeamMember', 'Model');
App::import('Service', 'TeamSsoSettingService');

class TeamsController extends BaseApiController
{
    /**
     * Endpoint for getting sso setting of a team
     *
     * @return ApiResponse|BaseApiResponse|CakeResponse|ErrorResponse
     */
    public function get_sso_setting()
    {
        $response = $this->validateGetSetting();

        if (!empty($response)) {
            return $response;
        }

        try {
            /** @var TeamSsoSettingService $TeamSsoSettingService */
            $TeamSsoSettingService = ClassRegistry::init('TeamSsoSettingService');
            $data = $TeamSsoSettingService->getSetting($this->getTeamId());
        } catch (Exception $e) {
            GoalousLog::error(
                "Failed to get SSO setting",
                [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'team_id' => $this->getTeamId()
                ]
            );

            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withData($data)->getResponse();
    }

    /**
     * Endpoint for storing sso setting
     *
     * @return ApiResponse|BaseApiResponse|ErrorResponse
     */
    public function post_sso_setting()
    {
        $this->validatePostSetting();

        if (!empty($response)) {
            return $response;
        }

        $data = $this->getRequestJsonBody();

        try {
            /** @var TeamSsoSettingService $TeamSsoSettingService */
            $TeamSsoSettingService = ClassRegistry::init('TeamSsoSettingService');
            $TeamSsoSettingService->addOrUpdateSetting($this->getTeamId(), $data['endpoint'], $data['idp_issuer'], $data['public_cert']);
        } catch (Exception $e) {
            GoalousLog::error(
                "Failed to post SSO setting",
                [
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                    'team_id' => $this->getTeamId()
                ]
            );

            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->getResponse();
    }

    private function validateGetSetting(): ?CakeResponse
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        if (!$TeamMember->isActiveAdmin($this->getUserId(), $this->getTeamId())) {
            return ErrorResponse::forbidden()->getResponse();
        }

        return null;
    }

    private function validatePostSetting(): ?CakeResponse
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        if (!$TeamMember->isActiveAdmin($this->getUserId(), $this->getTeamId())) {
            return ErrorResponse::forbidden()->getResponse();
        }

        $requestBody = $this->getRequestJsonBody();

        try {
            TeamRequestValidator::createPostSsoSettingValidator()->validate($requestBody);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            return ErrorResponse::badRequest()
                ->addErrorsFromValidationException($e)
                ->getResponse();
        } catch (Exception $e) {
            GoalousLog::error('Unexpected validation exception', [
                'class'   => get_class($e),
                'message' => $e,
            ]);
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return null;
    }
}
