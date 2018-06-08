<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('CircleFeedPaging', 'Services/Paging');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/06
 * Time: 13:07
 */

use Goalous\Enum\ApiVersion\ApiVersion as ApiVer;

class FeedsController extends BaseApiController
{
    use PagingControllerTrait;

    /**
     * Get list of circle feeds
     *
     * @return CakeResponse
     */
    public function get_circle_feed()
    {
        switch ($this->getApiVersion()) {
            case ApiVer::VER_2:
                return $this->get_circle_feed_v2();
                break;
            default:
                return $this->get_circle_feed_v2();
                break;
        }
    }

    /**
     * API V2 endpoint for getting list of circle feeds
     *
     * @return CakeResponse
     */
    public function get_circle_feed_v2()
    {
        $res = $this->validateGetCircleFeed();

        if (!empty($res)) {
            return $res;
        }

        /** @var CircleFeedPaging $CircleFeedPaging */
        $CircleFeedPaging = ClassRegistry::init('CircleFeedPaging');

        $data = $CircleFeedPaging->getDataWithPaging(
            $this->getPagingParameters($this->request),
            $this->getPagingLimit($this->request),
            CircleFeedPaging::EXTEND_ALL_FLAG);

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withData($data)->getResponse();
    }

    protected function getPagingConditionFromRequest(): PagingCursor
    {
        $PagingCursor = new PagingCursor();

        //Put current user's ID
        $condition['user_id'] = $this->getUser();

        $PagingCursor->addCondition($condition);
        $PagingCursor->addOrder('id', 'asc');

        return $PagingCursor;
    }

    protected function getResourceIdForCondition(): array
    {
        return [];
    }

    /**
     * Request & parameters validation before data manipulation
     *
     * @return CakeResponse
     */
    private function validateGetCircleFeed()
    {
        $res = $this->allowMethod('get');

        if (!empty($res)) {
            return $res;
        }
    }
}
