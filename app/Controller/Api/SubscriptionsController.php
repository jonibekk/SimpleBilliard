<?php
App::import('Service', 'SubscriptionService');
App::uses('BasePagingController', 'Controller/Api');
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;


class SubscriptionsController extends BasePagingController
{
    public function post_addSubscription()
    {
        /** @var SubscriptionService $SubscriptionService */
        $SubscriptionService = ClassRegistry::init("SubscriptionService");

        $requestData = $this->getRequestJsonBody();

        
        $validationError = $this->validateCreate($requestData);
        if ($validationError !== null) {
            return $validationError;
        }

        try {
            $userId = $this->getUserId();
            $subscription = $requestData;
            $res = $SubscriptionService->updateSubscription($userId, $subscription);
        } catch (Exception $e) {
            GoalousLog::error("Add subscription failed! ", [
                     'message'   => $e->getMessage(),
                     'trace'     => $e->getTraceAsString(),
                     'request_data'   => $requestData
                 ]);
            return ErrorResponse::internalServerError()
                ->withMessage(__($e->getMessage()))
                ->getResponse();
        }
        $ret['message'] = 'OK';
        if (!$res) {
            GoalousLog::warning('Add subscription failed! Request data: ', $requestData);
            $ret['message'] = 'failed';
        }

        return ApiResponse::ok()->withData($ret)->getResponse();
    }

    public function post_deleteSubscription()
    {
        /** @var SubscriptionService $SubscriptionService */
        $SubscriptionService = ClassRegistry::init("SubscriptionService");

        $requestData = $this->getRequestJsonBody();

        
        $validationError = $this->validateDelete($requestData);
        if ($validationError !== null) {
            return $validationError;
        }

        try {
            $userId = $this->getUserId();
            $subscription = $requestData;
            $res = $SubscriptionService->delete($userId, $subscription);
        } catch (Exception $e) {
            GoalousLog::error("Delete subscription failed! ", [
                     'message'   => $e->getMessage(),
                     'trace'     => $e->getTraceAsString(),
                     'request_data'   => $requestData
                 ]);
            return ErrorResponse::internalServerError()
                ->withMessage(__($e->getMessage()))
                ->getResponse();
        }
        $ret['message'] = 'OK';
        if (!$res) {
            GoalousLog::warning('Delete subscription failed! Request data: ', $requestData);
            $ret['message'] = 'Failed';
        }

        return ApiResponse::ok()->withData($ret)->getResponse();
    }
    

    private function validateCreate($data)
    {
        if ($this->validateSubscription($data)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        return null;
    }
    
    private function validateDelete($data)
    {
        if ($this->validateSubscription($data)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        return null;
    }

    private function validateCheck($data)
    {
        if ($this->validateSubscription($data)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        return null;
    }

    private function validateSubscription($data)
    {
        if (empty($data) or !is_array($data) or !isset($data['endpoint']) or !isset($data['keys']) or !isset($data['keys']['p256dh']) or !isset($data['keys']['auth'])) {
            return true;
        }

        return null;
    }
}
