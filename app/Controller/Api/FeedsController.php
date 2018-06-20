<?php
App::uses('BaseApiController', 'Controller/Api');
App::import('Service/Paging', 'CircleFeedPagingService');
App::uses('PagingControllerTrait', 'Lib/Paging');
App::uses('PagingCursor', 'Lib/Paging');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/06
 * Time: 13:07
 */
class FeedsController extends BaseApiController
{
    use PagingControllerTrait;

    /**
     * API endpoint for getting list of circle feeds
     *
     * @return CakeResponse
     */
    public function get_circle_feed()
    {
        return (new ApiResponse(ApiResponse::RESPONSE_GONE))->getResponse();

        $res = $this->validateGetCircleFeed();

        if (!empty($res)) {
            return $res;
        }

        /** @var CircleFeedPagingService $CircleFeedPaging */
        $CircleFeedPaging = ClassRegistry::init('CircleFeedPagingService');

        $data = $CircleFeedPaging->getDataWithPaging(
            $this->getPagingParameters($this->request),
            $this->getPagingLimit($this->request),
            CircleFeedPagingService::EXTEND_ALL);

//        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withBody($data)->getResponse();
    }

    protected function getPagingConditionFromRequest(): PagingCursor
    {
        $PagingCursor = new PagingCursor();

        //Put current user's ID
        $condition['user_id'] = $this->getUserId();
        $condition['team_id'] = $this->getTeamId();

        $PagingCursor->addCondition($condition);
        $PagingCursor->addOrder('id', 'asc');

        return $PagingCursor;
    }

    /**
     * Request & parameters validation before data manipulation
     *
     * @return CakeResponse | null
     */
    private function validateGetCircleFeed()
    {
        return null;
    }
}
