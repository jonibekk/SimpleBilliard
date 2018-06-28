<?php
App::uses('BaseApiController', 'Controller/Api');
App::import('Service', 'CircleListPagingService');

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

        $pagingCursor = $this->getPagingParameters();
        $pagingCursor->addResource('user_id', $userId);
        $pagingCursor->addCondition(['team_id' => $this->getTeamId()]);
        $pagingCursor->addCondition(['joined' => $this->request->query('joined') ?? true]);

        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        $circleData = $CircleListPagingService->getDataWithPaging($pagingCursor, $this->getPagingLimit());

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withData($circleData)->getResponse();
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