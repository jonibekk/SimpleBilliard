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


    /*
     * Parse price plan code
     * Ex. code "1-2"→ ["group_id" => 1, "detail_no" => 2"]
     * @param string $code
     *
     * @return array
     */
    static function parsePlanCode(string $code): array
    {
        try {
            $ar = explode('-', $code);
            if (count($ar) != 2) {
                throw new Exception(sprintf("Failed to parse price plan code. code:%s", $code));
            }
            if (!AppUtil::isInt($ar[0]) || !AppUtil::isInt($ar[1])) {
                throw new Exception(sprintf("Failed to parse price plan code. %s", AppUtil::jsonOneLine($ar)));
            }
            $res = ['group_id' => $ar[0], 'detail_no' => $ar[1]];
        } catch (Exception $e) {
            throw $e;
        }
        return $res;
    }
}
