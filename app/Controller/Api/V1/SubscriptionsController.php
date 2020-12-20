<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'SubscriptionService');

/**
 * Class SubscriptionsController
 */
class SubscriptionsController extends ApiController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Security->validatePost = false;
        $this->Security->csrfCheck = false;
    }

    public function post_addSubscription()
    {
        /** @var SubscriptionService $SubscriptionService */
        $SubscriptionService = ClassRegistry::init("SubscriptionService");

        $body = $this->request->input();
        $requestData = json_decode($body, true);
        
        $validationError = $this->validateCreate($requestData);
        if ($validationError !== null) {
            return $validationError;
        }

        try {
            $userId = $this->Auth->user('id');
            $subscription = $requestData;
            $res = $SubscriptionService->updateSubscription($userId, $subscription);
        } catch (Exception $e) {
            GoalousLog::error("Add subscription failed! ", [
                     'message'   => $e->getMessage(),
                     'trace'     => $e->getTraceAsString(),
                     'request_data'   => $requestData
                 ]);
            return $this->_getResponseInternalServerError();
        }
        $ret['message'] = 'OK';
        if (!$res) {
            GoalousLog::warning('Add subscription failed! Request data: ', $requestData);
            $ret['message'] = 'failed';
        }

        return $this->_getResponseSuccess(['data' => $ret]);
    }

    public function get_has_session()
    {
        return $this->_getResponseSuccess(['has_session' => true]);
    }

    private function validateCreate($data)
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
