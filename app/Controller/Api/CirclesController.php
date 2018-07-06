<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::import('Service/Paging', 'CirclePostPagingService');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('CircleMember', 'Model');
App::uses('Circle', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/20
 * Time: 9:41
 */
class CirclesController extends BasePagingController
{
    public function get_posts(int $circleId)
    {
        $error = $this->validateGetCirclePost($circleId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = ClassRegistry::init('CirclePostPagingService');

        try {
            $pagingRequest = $this->getPagingParameters();
        } catch (Exception $e) {
            return ErrorResponse::badRequest()->withException($e)->getResponse();
        }

        try {
            $data = $CirclePostPagingService->getDataWithPaging(
                $pagingRequest,
                $this->getPagingLimit(),
                $this->getExtensionOptions() ?? $this->getDefaultPostExtension());
        } catch (Exception $e) {
            GoalousLog::error($e->getMessage(), $e->getTrace());
            return ErrorResponse::internalServerError()->withException($e)->getResponse();
        }

        return ApiResponse::ok()->withBody($data)->getResponse();
    }

    /**
     * Validation for endpoint get_posts
     *
     * @param int $circleId
     *
     * @return CakeResponse|null
     */
    private function validateGetCirclePost(int $circleId)
    {
        if (!is_int($circleId)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        //Check if circle belongs to current team & user has access to the circle
        if (!$Circle->isBelongCurrentTeam($circleId, $this->getTeamId()) ||
            ($Circle->isSecret($circleId) && !$CircleMember->isBelong($circleId, $this->getUserId()))) {
            return ErrorResponse::forbidden()->withMessage(__("The circle dosen't exist or you don't have permission."))
                                ->getResponse();
        }

        return null;
    }

    /**
     * Defaut extension options for getting circle post
     *
     * @return array
     */
    private function getDefaultPostExtension()
    {
        return [
            CirclePostPagingService::EXTEND_CIRCLE,
            CirclePostPagingService::EXTEND_LIKE,
            CirclePostPagingService::EXTEND_SAVED
        ];
    }
}