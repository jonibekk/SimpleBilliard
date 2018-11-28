<?php
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::import('Lib/Paging', 'PagingRequest');
App::import('Service/Paging', 'CircleListPagingService');
App::uses('BaseV1PagingController', 'Controller/Api/V1');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 11/28/2018
 * Time: 3:20 PM
 */
class CirclesController extends BaseV1PagingController
{
    /**
     * API for searching circle
     *
     * @return BaseApiResponse
     */
    public function get_search()
    {
        try {
            $pagingRequest = $this->getPagingParameters();

            $pagingRequest->addQueriesToCondition(['keyword']);

            if (empty($pagingRequest->getQuery('joined'))) {
                //When searching, include not joined public circles by default unless specified
                $pagingRequest->addCondition(['joined' => 0]);
            }

            if (empty($pagingRequest->getConditions()['keyword'])) {
                return ErrorResponse::badRequest()->withMessage(__("Please enter text."))->getResponse();
            }

        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        $circleData = $CircleListPagingService->getDataWithPaging($pagingRequest, $this->getPagingLimit(),
            $this->getExtensionOptions() ?: $this->getDefaultCircleExtension());

        return ApiResponse::ok()->withBody($circleData)->getResponse();
    }

    /**
     * Default extension option for searching circle
     *
     * @return array
     */
    private function getDefaultCircleExtension()
    {
        return [CircleListPagingService::EXTEND_MEMBER_INFO];
    }
}