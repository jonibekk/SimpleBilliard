<?php
App::import('Service', 'FcmSubscriberService');
App::uses('BasePagingController', 'Controller/Api');
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;


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
            "endpoint" => "https://updates.push.services.mozilla.com/wpush/v2/gAAAAABfPitMZhBMK4oAUjZOPffR9nO3kcXc5m8GD0NKfYUeZkep_mzxPDVAbc7oIKsUGgya18WzPg4d-Ww2-6QwMjL9Mb5O0SuQK6bdnkuHI2YejjXEiBMFlT0AANEqj4BhnZxGJgAZ2hvrgeZleY0mahy1vq7pIIgbmxvbapYpXOeegRL_Lt4",
            "keys" => [
                "auth" => "76pYo_IxvTkw6L8znvCrOg",
                "p256dh" => "BEY7koFbcxmPHgjpv-1l_ImRbZuB-1tk3oCadu1phXpgLQ70hXt4w4_flp8DRz4u7FO4iSj0yWmWLM80wGtaizE"
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

    private function validateCreate($data)
    {
        return null;
    }
}
