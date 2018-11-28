<?php
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::import('Lib/Paging', 'PagingRequest');
App::import('Service/Paging', 'TeamMemberPagingService');
App::uses('BaseV1PagingController', 'Controller/Api/V1');

/**
 * Class UsersController
 */
class UsersController extends BaseV1PagingController
{
    public function get_search()
    {
        try {
            $pagingRequest = $this->getPagingParameters();

            $pagingRequest->addQueriesToCondition(['keyword']);

            if (empty($pagingRequest->getConditions()['keyword'])) {
                return ErrorResponse::badRequest()->withMessage(__("Please enter text."))->getResponse();
            }

            $pagingRequest->addCondition(['excluded_ids' => [$this->Auth->user('id')]]);
            $pagingRequest->addCondition(['lang' => $this->Auth->user('language')]);

        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var TeamMemberPagingService $TeamMemberPagingService */
        $TeamMemberPagingService = ClassRegistry::init('TeamMemberPagingService');

        $userList = $TeamMemberPagingService->getDataWithPaging($pagingRequest, $this->getPagingLimit(),
            $this->getExtensionOptions() ?: $this->getDefaultTeamMemberExtension());

        return ApiResponse::ok()->withBody($userList)->getResponse();
    }

    /**
     * Default extension option for searching circle
     *
     * @return array
     */
    private function getDefaultTeamMemberExtension()
    {
        return [TeamMemberPagingService::EXTEND_USER];
    }
}
