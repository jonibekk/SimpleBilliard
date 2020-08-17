<?php
App::import('Service', 'FcmSubscriberService');

class FcmSubscribersController extends BasePagingController
{
    public function post_addSubscriber()
    {
        /** @var FcmSubscriberService $FcmSubscirberService */
        $FcmSubscirberService = ClassRegistry::init("FcmSubscriberService");

        $requestData = $this->getRequestJsonBody();
        GoalousLog::error(print_r($requestData, true));

        /*
        $validationError = $this->validateCreate($requestData);
        if ($validationError !== null) {
            return $validationError;
        }

        try {
            $user_id = $this->getUserId();
            $subscriber = $requestData['subscriber'];
            $res = $FcmSubscirberService->add($user_id, $subscriber);
        } catch (Exception $e) {
            return ErrorResponse::internalServerError()
                ->withMessage(__($e->getMessage()))
                ->getResponse();
        }
        if (!$res) {
            GoalousLog::warning('add subscirber failed', $requestData);
        }
         */

        $ret['message'] = 'OK';
        return ApiResponse::ok()->withData($ret)->getResponse();
    }

    private function validateCreate($data)
    {
        return null;
    }
}
