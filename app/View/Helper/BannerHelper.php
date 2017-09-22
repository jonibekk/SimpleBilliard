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
     * @param string $teamCreditCardExpireDate
     * @param bool   $statusPaymentFailed
     *
     * @return string
     */
    public function getBannerMessage(
        int $serviceUseStatus,
        bool $isTeamAdmin,
        $stateEndDate,
        int $teamCreditCardStatus,
        string $teamCreditCardExpireDate,
        bool $statusPaymentFailed
    ): string {
        // TODO: this Helper needs to be mode flexibility
        // if developer adding another banner message pattern next time
        // this method should be like ...
        //  public function getBannerMessage(InterfaceBannerMessage $bannerMessage): string
        // OR Controller should wrap array required params.

        // TODO.payment: $stateEndDate should be type hinted. but, disable type hint temporary.
        if ($stateEndDate) {
            $stateEndDate = $this->TimeEx->formatYearDayI18nFromDate($stateEndDate);
        }
        if ($teamCreditCardExpireDate) {
            $teamCreditCardExpireDate = $this->TimeEx->formatYearDayI18nFromDate($teamCreditCardExpireDate);
        }

        if ($statusPaymentFailed) {
            return __('Your last payment have been unsuccessful.');
        }

        switch ($serviceUseStatus) {
            case Team::SERVICE_USE_STATUS_READ_ONLY:
                if ($isTeamAdmin) {
                    return __('Your team will remain in a read-only state until <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. If you want to resume normal usage, please <a href="/payments">subscribe</a> to our payment plan.',
                        $stateEndDate);
                } else {
                    return __('Your team will remain in a read-only state until <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. If you want to resume normal usage, please contact to your team administrators.',
                        $stateEndDate);
                }
            case Team::SERVICE_USE_STATUS_FREE_TRIAL:
                if ($isTeamAdmin) {
                    return __('Your free trial of Goalous will end on <strong>%s</strong>. Following this date your team will be in a read-only state, and will not be able to post new content. If you want to resume normal usage, please <a href="/payments">subscribe</a> to our payment plan.',
                        $stateEndDate);
                } else {
                    return __('Your free trial of Goalous will end on <strong>%s</strong>. Following this date your team will be in a read-only state, and will not be able to post new content. If you want to resume normal usage, please contact to your team administrators.',
                        $stateEndDate);
                }
        }

        if ($isTeamAdmin) {
            switch ($teamCreditCardStatus) {
                case Team::STATUS_CREDIT_CARD_EXPIRED:
                    return __('Your credit card has expired. You will no longer be able to use Goalous. Update credit card from <a href="/payments/method">here</a>.',
                        $teamCreditCardExpireDate);
                case Team::STATUS_CREDIT_CARD_EXPIRE_SOON:
                    return __('Your credit card expires on <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. Update credit card from <a href="/payments/method">here</a>.',
                        $teamCreditCardExpireDate);
            }
        }

        return '';
    }
}
