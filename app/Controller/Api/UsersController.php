<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Service/Paging', 'CircleListPagingService');
App::import('Lib/Paging', 'PagingRequest');
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/04
 * Time: 15:07
 */
class UsersController extends BasePagingController
{
    /**
     * Get list of circles that an user is joined in
     *
     * @param int $userId
     *
     * @return CakeResponse
     */
    public function get_circles(int $userId)
    {
        $res = $this->validateCircles();

        if (!empty($res)) {
            return $res;
        }
        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        $circleData = $CircleListPagingService->getDataWithPaging($pagingRequest, $this->getPagingLimit(),
            $this->getExtensionOptions());

        return ApiResponse::ok()->withBody($circleData)->getResponse();
    }

    protected function getPagingConditionFromRequest(): PagingRequest
    {
        $pagingRequest = new PagingRequest();
        $pagingRequest->addOrder('latest_post_created');
        return $pagingRequest;
    }

    /**
     * Parameter validation for circles()
     *
     * @return CakeResponse | null
     */
    private function validateCircles()
    {
        return null;
    }
}