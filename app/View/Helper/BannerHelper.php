<?php
App::uses('Banner', 'View/Helper');

/**
 * Class BannerHelper
 *
 * @property TimeExHelper $TimeEx
 */
class BannerHelper extends AppHelper
{
    public $helpers = array(
        'TimeEx',
    );

    /**
     * getBannerMessage
     *
     * @param int    $serviceUseStatus
     * @param bool   $isTeamAdmin
     * @param string $stateEndDate
     *
     * @return string
     */
    public function getBannerMessage(int $serviceUseStatus, bool $isTeamAdmin, string $stateEndDate): string
    {
        $stateEndDate = $this->TimeEx->formatYearDayI18nFromDate($stateEndDate);

        if ($serviceUseStatus == Team::SERVICE_USE_STATUS_READ_ONLY) {
            if ($isTeamAdmin) {
                return __('Your team will remain in a read-only state until <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. If you want to resume normal usage, please <a href="/payments/apply">subscribe</a> to our payment plan.',
                    $stateEndDate);
            } else {
                return __('Your team will remain in a read-only state until <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. If you want to resume normal usage, please contact to your team administrators.',
                    $stateEndDate);
            }
        } elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_FREE_TRIAL) {
            if ($isTeamAdmin) {
                return __('Your free trial of Goalous will end on <strong>%s</strong>. Following this date your team will be in a read-only state, and will not be able to post new content. If you want to resume normal usage, please <a href="/payments/apply">subscribe</a> to our payment plan.',
                    $stateEndDate);
            } else {
                return __('Your free trial of Goalous will end on <strong>%s</strong>. Following this date your team will be in a read-only state, and will not be able to post new content. If you want to resume normal usage, please contact to your team administrators.',
                    $stateEndDate);
            }
        }
    }
}
