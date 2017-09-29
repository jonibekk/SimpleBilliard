<?php

/**
 * Class PaymentUtil
 */
class PaymentUtil
{
    /**
     * log current charge users of specific team
     * this log is use for the recovering monthly payment
     * @see https://confluence.goalous.com/display/GOAL/Payments+Recovery
     *
     * @param int $teamId
     * @return void
     */
    static function logCurrentTeamChargeUsers(int $teamId)
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $amountChargeUsers = $TeamMember->countChargeTargetUsers($teamId);
        $text = sprintf(
            'current charge user of team: %s',
            AppUtil::jsonOneLine([
                'teams.id'     => $teamId,
                'charge_users' => $amountChargeUsers,
            ])
        );
        CakeLog::info($text);
    }
}
