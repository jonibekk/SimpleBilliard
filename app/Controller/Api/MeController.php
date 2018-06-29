<?php
App::uses('BasePagingController', 'Controller/Api');
App::import('Service/Paging', 'CircleListPagingService');
App::import('Lib/Paging', 'PagingCursor');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/29
 * Time: 11:47
 */
class MeController extends BasePagingController
{
    /**
     * Get list of circles that an user is joined in
     *
     * @param int $userId
     *
     * @return CakeResponse
     */
    public function get_circles()
    {

        $res = $this->validateCircles();

        if (!empty($res)) {
            return $res;
        }
        try {
            $pagingCursor = $this->getPagingParameters();
        } catch (Exception $e) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->withException($e)->getResponse();
        }
        $pagingCursor->addCondition(['user_id' => $this->getUserId()]);
        $pagingCursor->addCondition(['team_id' => $this->getTeamId()]);
        $pagingCursor->addCondition(['joined' => boolval($this->request->query('joined')) ?? true]);
        $pagingCursor->addCondition(['public_only' => boolval($this->request->query('public_only')) ?? true]);

        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        $circleData = $CircleListPagingService->getDataWithPaging($pagingCursor, $this->getPagingLimit(), [CircleListPagingService::EXTEND_MEMBER_INFO]);

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withBody($circleData)->getResponse();
    }

    protected function getPagingConditionFromRequest(): PagingCursor
    {
        $pagingCursor = new PagingCursor();
        $pagingCursor->addOrder('latest_post_created');
        return $pagingCursor;
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