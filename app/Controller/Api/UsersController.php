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

        $isPublic = $this->request->query('is_public');

        /** @var CircleMemberService $CircleMemberService */
        $CircleMemberService = ClassRegistry::init('CircleMemberService');

        $circleData = $CircleMemberService->getUserCircles($userId, $this->getTeamId(), $isPublic);

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withData($circleData)->getResponse();
    }

    /**
     * Parameter validation for circles()
     *
     * @return CakeResponse
     */
    private function validateCircles(): CakeResponse
    {
        $res = $this->allowMethod('GET');

        if (!empty($res)) {
            return $res;
        }
    }

}