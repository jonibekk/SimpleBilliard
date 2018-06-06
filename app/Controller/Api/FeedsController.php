<?php
App::uses('BaseApiController', 'Controller/Api');
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/06
 * Time: 13:07
 */

class FeedsController extends BaseApiController
{
    /**
     * Get list of circle feeds
     */
    public function get_circle_feed()
    {
        switch ($this->getApiVersion()) {
            case 2:
                return $this->get_circle_feed_v2();
                break;
            default:
                return $this->get_circle_feed_v2();
                break;
        }
    }

    public function get_circle_feed_v2()
    {
        $res = $this->validateGetCircleFeed();

        if (!empty($res)) {
            return $res;
        }

        /** @var CircleFeedPaging $CircleFeedPaging */
        $CircleFeedPaging = ClassRegistry::init('CircleFeedPaging');

        //TODO
    }

    private function validateGetCircleFeed()
    {
        $res = $this->allowMethod('get');

        if (!empty($res)) {
            return $res;
        }
    }
}
