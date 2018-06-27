<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('CircleMember', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/27
 * Time: 11:13
 */
class CircleMembersController extends BaseApiController
{

    /**
     * Endpoint for getting the detail about a circle member for an user
     *
     * @param int $circleId
     *
     * @return CakeResponse
     */
    public function get_detail(int $circleId): CakeResponse
    {
        $error = $this->validateGetDetail($circleId);
        if (!empty($error)) {
            return $error;
        }

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $result = $CircleMember->getCircleMember($circleId, $this->getUserId());

        return (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))->withData($result)->getResponse();
    }

    /**
     * @param int $circleId
     *
     * @return null| CakeResponse
     */
    private function validateGetDetail(int $circleId)
    {
        if (!is_int($circleId)) {
            return (new ApiResponse(ApiResponse::RESPONSE_BAD_REQUEST))->getResponse();
        }

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        if (!$CircleMember->isJoined($circleId, $this->getUserId())) {
            return (new ApiResponse(ApiResponse::RESPONSE_FORBIDDEN))->withMessage(__("The circle dosen't exist or you don't have permission."))
                                                                     ->getResponse();
        }

        return null;
    }
}