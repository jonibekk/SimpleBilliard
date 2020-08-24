<?php
App::import('Service', 'AppService');
App::uses('User', 'Model');
App::uses("Subscription", 'Model');
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

use Goalous\Enum as Enum;
/**
 * Class UserService
 */
class SubscriptionService extends AppService
{
    /**
     * get users' subscription
     * extend data
     *
     * @param $userid 
     *
     * @return array
     */
    public function add($user_id, $subscription)
    {
        /** @var Subscription $Subscription */
        $Subscription = ClassRegistry::init('Subscription');
        $res = $Subscription->add($user_id, $subscription);
        if (!$res) {
            GoalousLog::warning('Add subscription failed! Params: ', ['user_id' => $user_id, 'subscription' => $subscription]);
        }
        return $res;
    }

    /**
     * get users' fcm subscription
     * extend data
     *
     * @param $userid 
     *
     * @return array
     */
    public function getSubscriptionByUserId($user_id): array
    {
        /** @var Subscription $Subscription */
        $Subscription = ClassRegistry::init('Subscription');
        $res =  $Subscription->getSubscriptionByUserId($user_id);
        if (!$res) {
            GoalousLog::warning('get subscription failed! Params: ', ['user_id' => $user_id]);
        }
        return $res;
    }

    /**
     * delete users' fcm subscription
     * extend data
     *
     * @param $userid 
     *
     * @return array
     */
    public function delete($user_id, $subscription)
    {
        /** @var Subscription $Subscription */
        $Subscription = ClassRegistry::init('Subscription');
        $res = $Subscription->deleteSubscription($user_id, $subscription);
        if (!$res) {
            GoalousLog::warning('delete subscription failed! Params: ', ['user_id' => $user_id, 'subscription' => $subscription]);
        }
        return $res;
    }

    /**
     * update subscription
     * extend data
     *
     * @param $userid 
     *
     * @return array
     */
    public function updateSubscription($user_id, $subscription)
    {
        /** @var Subscription $Subscription */
        $Subscription = ClassRegistry::init('Subscription');
        $res = $Subscription->updateSubscription($user_id, $subscription);
        if (!$res) {
            GoalousLog::warning('update subscription failed! Params: ', ['user_id' => $user_id, 'subscription' => $subscription]);
        }
        return $res;
    }

    /**
     * update subscription
     * extend data
     *
     * @param $userid 
     *
     * @return array
     */
    public function sendDesktopPushNotification($subscriptions, $title, $postUrl)
    {
        $sendData = [
            "notification" => [
                "title" => $title,
                "body" => '',
                "url" => "https://e2e.goalous.com/circles",
                "data" => [
                    "url" => $postUrl,
                    "type" => "circle"
                ]
            ]
        ];

        $auth = [
            'VAPID' => [
                'subject' => 'mailto:ning.li@colorkrew.com', // can be a mailto: or your website address
                'publicKey' => 'BADPfHVYNRRsWtC9p5_PiWPQF6dTAziyKUUZf1AEl1Jxyq_vbDZdwuvrnpGHK1KejKjAZD1xylESIk3ywANhm7Q', // (recommended) uncompressed public key P-256 encoded in Base64-URL
                'privateKey' => 'eV6SQy32nZKfniyZHPvS3glCm_8AGkOitktQ8opaPnc', // (recommended) in fact the secret multiplier of the private key encoded in Base64-URL
            ],
        ];

        $webPush = new WebPush($auth);
        $sendDataJson = json_encode($sendData);
                
        foreach ($subscriptions as $subscription){
            $report = $webPush->sendNotification(
                Subscription::create(json_decode($subscription['Subscription']['subscription'], true)),
                $sendDataJson
            );
        }
        
        // $report = $webPush->flush();
        foreach($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            if (!$report->isSuccess()){
                GoalousLog::warning('Send desktop notification failed. Params: ', ['endpoint' => $endpoint, 'reason' => $report->getReason()]);
            }
        }
    }
}
