<?php
App::uses('Banner', 'View/Helper');

class BannerHelper extends AppHelper
{
    public function getBannerMessage($serviceUseStatus, $isTeamAdmin) {
        if($serviceUseStatus == Team::SERVICE_USE_STATUS_READ_ONLY) {
            if ($isTeamAdmin) {
                return 'Your team will remain in a read-only state until <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. If you want to resume normal usage, please <a href="/payments/apply">subscribe</a> to our payment plan.';
            }else{
                return 'Your team will remain in a read-only state until <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. If you want to resume normal usage, please contact to your team administrators.';
            }
        }elseif($serviceUseStatus == Team::SERVICE_USE_STATUS_FREE_TRIAL) {
            if ($isTeamAdmin) {
                return 'Your free trial of Goalous will end on <strong>%s</strong>. Following this date your team will be in a read-only state, and will not be able to post new content. If you want to resume normal usage, please <a href="/payments/apply">subscribe</a> to our payment plan.';
            }else{
                return 'Your free trial of Goalous will end on <strong>%s</strong>. Following this date your team will be in a read-only state, and will not be able to post new content. If you want to resume normal usage, please contact to your team administrators.';
            }
        }else{
            return $serviceUseStatus.' has not been given a banner message.';
        }
    }
}