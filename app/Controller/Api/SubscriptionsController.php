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
        GoalousLog::error(print_r($requestData, true));

        
        $validationError = $this->validateCreate($requestData);
        if ($validationError !== null) {
            return $validationError;
        }

        try {
            $userId = $this->getUserId();
            $subscription = $requestData;
            $res = $SubscriptionService->updateSubscription($userId, $subscription);
        } catch (Exception $e) {
            GoalousLog::error('Add subscription failed'. $e->getMessage());
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

    public function post_checkSubscription()
    {
        /** @var SubscriptionService $SubscriptionService */
        $SubscriptionService = ClassRegistry::init("SubscriptionService");

        $requestData = $this->getRequestJsonBody();
        GoalousLog::error(print_r($requestData, true));

        
        $validationError = $this->validateCheck($requestData);
        if ($validationError !== null) {
            return $validationError;
        }

        try {
            $userId = $this->getUserId();
            $subscription = $requestData;
            $res = $SubscriptionService->check($userId, $subscription);
        } catch (Exception $e) {
            GoalousLog::error('Checke subscription failed'. $e->getMessage());
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

    public function post_deleteSubscription()
    {
        /** @var SubscriptionService $SubscriptionService */
        $SubscriptionService = ClassRegistry::init("SubscriptionService");

        $requestData = $this->getRequestJsonBody();
        GoalousLog::error(print_r($requestData, true));

        
        $validationError = $this->validateDelete($requestData);
        if ($validationError !== null) {
            return $validationError;
        }

        try {
            $userId = $this->getUserId();
            $subscription = $requestData;
            $res = $SubscriptionService->delete($userId, $subscription);
        } catch (Exception $e) {
            GoalousLog::error('Delete subscription failed'. $e->getMessage());
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

    public function post_sendNotification()
    {
        /** @var SubscriptionService $SubscriptionService */
        $SubscriptionService = ClassRegistry::init("SubscriptionService");
        
        $userId = $this->getUserId();
        $subscriptions = $SubscriptionService->getSubscriptionByUserId($userId);
        $SubscriptionService->sendDesktopPushNotification($subscriptions, 'test title', 'test url');
        GoalousLog::error(print_r($subscriptions, true));
        return ApiResponse::ok()->withData($subscriptions)->getResponse();
    }
    /*
    public function post_sendNotification()
    {
        $sendData = [
            "notification" => [
                "title" => "Angular News",
                "body" => "Newsletter Available!",
                "url" => "https://e2e.goalous.com/circles",
                "data" => [
                    "url" => "https://e2e.goalous.com/circles",
                    "type" => "circle"
                ]
            ]
        ];

        //
        // array of notifications
        $subscription = [
                    "endpoint" => "https://fcm.googleapis.com/fcm/send/cYufx4NzzBk:APA91bGbH1ex6J2nqllzKtkHkAU6uHlQPJgC8Zawj46GRssWC9OvAveE3UagsMzNZytLvCt1dC4Z9CBwPkItahh5W7CrpH5zriTsqmk2W8eJFqOxnsyW0JAVjGITPP9_1XhJJmkTVRva",
                    "keys" => [
                        "p256dh" => "BFNkFGsxHxCzcJcIKunXxh4hDNUDG3ndocrzD07UOW0GM4nzsAuez10r-jfRC63zXCum6ibT9rm7S435YwCuq-I",
                        "auth" => "jd205uS8p3OaAqA0-a47sA"
                    ],
                ];
        $notifications = [
            [
                'subscription' => Subscription::create($subscription),
                'payload' => json_encode($sendData),
            ]
        ];
        $subscription = [
            "endpoint" => "https://updates.push.services.mozilla.com/wpush/v2/gAAAAABfP0qnbvBjP0G8omSJVcuxIFGaBQoUJLH5xTxQ_Lz4mGqTIM0tk3FEzSmZSYiKTpjst3xWcM58rNF--gou8dlfc2TXRLfMNYYzF3hHERGjUJNZ04SRYpo2Nao2lotufkdcBiOJxPzunbk9IVxXau1n7DCLuN03epj_3vEjNcMTdjL7dFU",
            "keys" => [
                "auth" => "P7updCfJtuCRskwCFJQu1Q",
                "p256dh" => "BEBSYJWr_183TtEPaUffjbOIO7FyrNTG8I1jZgrWyG4ndCvnYUkvB2qJmRzlu9yP4rwspM2YQY7hGwpNm1_ohAE"
            ],
        ];


        $auth = [
            'VAPID' => [
                'subject' => 'mailto:ning.li@colorkrew.com', // can be a mailto: or your website address
                'publicKey' => 'BADPfHVYNRRsWtC9p5_PiWPQF6dTAziyKUUZf1AEl1Jxyq_vbDZdwuvrnpGHK1KejKjAZD1xylESIk3ywANhm7Q', // (recommended) uncompressed public key P-256 encoded in Base64-URL
                'privateKey' => 'eV6SQy32nZKfniyZHPvS3glCm_8AGkOitktQ8opaPnc', // (recommended) in fact the secret multiplier of the private key encoded in Base64-URL
            ],
        ];



        $webPush = new WebPush($auth);

                
        $report = $webPush->sendNotification(
            Subscription::create($subscription),
            // $notifications[0]['payload'], // optional (defaults null),
            json_encode($sendData)
        );
        // $report = $webPush->flush();
        $flushes = [];
        foreach($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            if ($report->isSuccess()){
                echo "[v] sucess {$endpoint}";
            } else {
                echo "[x] faild {$endpoint} : {$report->getReason()}";
            }
            $flushes = $report->getReason();
        }

        $ret['message'] = 'OK';
        $ret['request'] = $report->getRequest();
        $ret['response'] = $report->getResponse();
        $ret['isSuccess'] = $report->isSuccess();
        $ret['notifications'] = $notifications[0]['subscription'];
        $ret['payload'] = $notifications[0]['payload'];
        return ApiResponse::ok()->withData($ret)->getResponse();
    }
     */

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
            GoalousLog::error(print_r($data, true));
            return true;
        }

        return null;
    }
}
