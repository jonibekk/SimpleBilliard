<?php
App::uses('BaseApiController', 'Controller/Api');
App::import('Service/Paging', 'CirclePostPagingService');
App::uses('PagingControllerTrait', 'Lib/Paging');
App::uses('PagingCursor', 'Lib/Paging');
App::uses('CircleMember', 'Model');
App::uses('Circle', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/20
 * Time: 9:41
 */
class CirclesController extends BaseApiController
{
    use PagingControllerTrait;

    public function get_posts(int $circleId)
    {
        $error = $this->validateGetCirclePost($circleId);

        if (!empty($error)) {
            return $error;
        }

        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = ClassRegistry::init('CirclePostPagingService');

        $pagingCursor = $this->getPagingParameters($this->request);
        $pagingCursor->addCondition(['circle_id' => $circleId]);

        try {
            $data = $CirclePostPagingService->getDataWithPaging(
                $pagingCursor,
                $this->getPagingLimit($this->request),
                $this->getExtensionOptions($this->request) ?? CirclePostPagingService::EXTEND_ALL);
        } catch (Exception $e) {
            GoalousLog::error($e->getMessage(), $e->getTrace());
            return (new ApiResponse(ApiResponse::RESPONSE_INTERNAL_SERVER_ERROR))->withException($e)->getResponse();
        }

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withBody($data)->getResponse();
    }

    protected function getPagingConditionFromRequest(): PagingCursor
    {
        return new PagingCursor();
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
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->getResponse();
        }

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        //Check if circle belongs to current team & user has access to the circle
        if (!$Circle->isBelongCurrentTeam($circleId, $this->getTeamId()) ||
            ($Circle->isSecret($circleId) && !$CircleMember->isBelong($circleId, $this->getUserId()))) {
            return (new ApiResponse(ApiResponse::RESPONSE_FORBIDDEN))->withMessage(__("The circle dosen't exist or you don't have permission."))
                                                                     ->getResponse();
        }

        return null;
    }
}