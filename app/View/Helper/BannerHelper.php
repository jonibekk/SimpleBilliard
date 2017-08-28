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
     * @param int    $teamCreditCardStatus
     *
     * @return string
     */
    public function getBannerMessage(int $serviceUseStatus, bool $isTeamAdmin, string $stateEndDate, int $teamCreditCardStatus, string $teamCreditCardExpireDate): string
    {
        // TODO: this Helper needs to be mode flexibility
        // if developer adding another banner message pattern next time
        // this method should be like ...
        //  public function getBannerMessage(InterfaceBannerMessage $bannerMessage): string

        $stateEndDate = $this->TimeEx->formatYearDayI18nFromDate($stateEndDate);

        switch ($serviceUseStatus) {
            case Team::SERVICE_USE_STATUS_READ_ONLY:
                if ($isTeamAdmin) {
                    return __('Your team will remain in a read-only state until <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. If you want to resume normal usage, please <a href="/payments/apply">subscribe</a> to our payment plan.',
                        $stateEndDate);
                } else {
                    return __('Your team will remain in a read-only state until <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. If you want to resume normal usage, please contact to your team administrators.',
                        $stateEndDate);
                }
            case Team::SERVICE_USE_STATUS_FREE_TRIAL:
                if ($isTeamAdmin) {
                    return __('Your free trial of Goalous will end on <strong>%s</strong>. Following this date your team will be in a read-only state, and will not be able to post new content. If you want to resume normal usage, please <a href="/payments/apply">subscribe</a> to our payment plan.',
                        $stateEndDate);
                } else {
                    return __('Your free trial of Goalous will end on <strong>%s</strong>. Following this date your team will be in a read-only state, and will not be able to post new content. If you want to resume normal usage, please contact to your team administrators.',
                        $stateEndDate);
                }
        }

        if ($isTeamAdmin) {
            switch ($teamCreditCardStatus) {
                case Team::STATUS_CREDIT_CARD_EXPIRED:
                    return __('Your credit card expired. you will no longer be able to use Goalous. Update credit card from <a href="#">here</a>.',
                        $teamCreditCardExpireDate);
                case Team::STATUS_CREDIT_CARD_EXPIRE_SOON:
                    return __('Your credit card expires on <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. Update credit card from <a href="#">here</a>.',
                        $teamCreditCardExpireDate);
            }
        }

        return '';
    }
}
