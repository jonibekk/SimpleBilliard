<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/04
 * Time: 15:07
 */

class UsersController extends ApiV2Controller
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

        $circleMemberService = new CircleMemberService();

        $circleData = $circleMemberService->getUserCircles($userId, $this->getTeamId(), $isPublic);

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withData($circleData)->getResponse();
    }

    /**
     * Parameter validation for circles()
     *
     * @param int $userId
     *
     * @return CakeResponse
     */
    private function validateCircles(): CakeResponse
    {
        $res = $this->allowMethod('get');

        if (!empty($res)) {
            return $res;
        }
    }

}