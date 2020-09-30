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
     * add users' subscription
     *
     * @param $userid  $subscription
     *
     * @return array
     */
    public function add($userId, $subscription)
    {
        /** @var Subscription $Subscription */
        $Subscription = ClassRegistry::init('Subscription');
        $res = $Subscription->add($userId, $subscription);
        if (!$res) {
            GoalousLog::warning('Add subscription failed! Params: ', ['user_id' => $userId, 'subscription' => $subscription]);
        }
        return $res;
    }

    /**
     * get users' subscription
     * extend data
     *
     * @param $userid 
     *
     * @return array
     */
    public function getSubscriptionByUserId($userId): array
    {
        /** @var Subscription $Subscription */
        $Subscription = ClassRegistry::init('Subscription');
        $res =  $Subscription->getSubscriptionByUserId($userId);
        if (!$res) {
            GoalousLog::warning('get subscription failed! Params: ', ['user_id' => $userId]);
        }
        return $res;
    }

    /**
     * delete users' subscription
     *
     * @param $userid $Subscription
     *
     * @return bool
     */
    public function delete($userId, $subscription)
    {
        /** @var Subscription $Subscription */
        $Subscription = ClassRegistry::init('Subscription');
        $res = $Subscription->deleteSubscription($userId, $subscription);
        if (!$res) {
            GoalousLog::warning('delete subscription failed! Params: ', ['user_id' => $userId, 'subscription' => $subscription]);
        }
        return $res;
    }

    /**
     * update subscription
     *
     * @param $userid, $subscription 
     *
     * @return array
     */
    public function updateSubscription($userId, $subscription)
    {
        /** @var Subscription $Subscription */
        $Subscription = ClassRegistry::init('Subscription');
        $res = $Subscription->updateSubscription($userId, $subscription);
        if (!$res) {
            GoalousLog::warning('update subscription failed! Params: ', ['user_id' => $userId, 'subscription' => $subscription]);
        }
        return $res;
    }

    /**
     * check subscription
     *
     * @param $userid, $subscription 
     *
     * @return array
     */
    public function check($userId, $subscription)
    {
        /** @var Subscription $Subscription */
        $Subscription = ClassRegistry::init('Subscription');
        $res = $Subscription->checkSubscription($userId, $subscription);
        return $res;
    }

    /**
     * send Desktop push notification
     *
     * @param $userid 
     *
     * @return
     */
    public function sendDesktopPushNotification($subscriptions, $title, $postUrl)
    {
        $sendData = [
            "notification" => [
                "title" => $title,
                "body" => '',
                "url" => VAPID_SUBJECT,
                "data" => [
                    "url" => $postUrl,
                ]
            ]
        ];

        $auth = [
            'VAPID' => [
                'subject' => VAPID_SUBJECT, // can be a mailto: or your website address
                'publicKey' => VAPID_PUBLIC_KEY, // (recommended) uncompressed public key P-256 encoded in Base64-URL
                'privateKey' => VAPID_PRIVATE_KEY, // (recommended) in fact the secret multiplier of the private key encoded in Base64-URL
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
