<?php
App::uses('BaseApiController', 'Controller/Api');
App::import('Service', 'CircleMemberService');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/04
 * Time: 15:07
 */
class UsersController extends BaseApiController
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

        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');

        $circleData = $CircleMemberService->getUserCircles($userId, $this->getTeamId());

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withData($circleData)->getResponse();
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