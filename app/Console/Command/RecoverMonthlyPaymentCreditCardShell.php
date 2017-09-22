<?php
App::import('Service', 'PaymentService');
App::import('Service', 'InvoiceService');
App::uses('AppUtil', 'Util');

use Goalous\Model\Enum as Enum;

/**
 * creating credit card payment about passed parameters
 * for recovering credit card monthly payments
 *
 * this shell is not for crontabs/systemd to run automatically
 *
 * # usage
 * ./Console/cake recover_monthly_payment_credit_card --team_id=<teams.id> --amount_charge_users=<int> --target_date_time="<Y-m-d>"
 *
 * # required param
 * @param team_id int target teams.id
 * @param amount_charge_users int amount of users to paid
 * @param target_date_time string target date time it should be created paid at
 *
 * # sample command
 * ./Console/cake recover_monthly_payment_credit_card --team_id=7 --amount_charge_users=1 --target_date_time="2017-09-12"
 *
 * class RecoverMonthlyPaymentCreditCardShell
 *
 * @property Team             $Team
 * @property TeamMember       $TeamMember
 * @property PaymentSetting   $PaymentSetting
 * @property PaymentService   $PaymentService
 */
class RecoverMonthlyPaymentCreditCardShell extends AppShell
{
    public $uses = [
        'Team',
        'TeamMember',
        'PaymentSetting',
        'PaymentService',
    ];

    public function startup()
    {
        parent::startup();
    }

    /**
     * @return ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $parser->addOptions([
            'team_id' => [
                'help'     => '@param int required'
                    . PHP_EOL . 'team id to recover monthly invoice payment',
            ],
            'amount_charge_users' => [
                'help'     => '@param int'
                    . PHP_EOL . 'amount of charge users to paid',
            ],
            'target_date_time' => [
                'help'     => '@param string "Y-m-d" required'
                    . PHP_EOL . 'target date of recovering invoice payment',
            ],
        ]);
        return $parser;
    }

    private function getValuesFromOption(): array
    {
        // validate option: target team
        $teamId = $this->param('team_id');
        if (!AppUtil::isInt($teamId)) {
            throw new InvalidArgumentException(sprintf('option team_id must be int: %s', $teamId));
        }
        $team = $this->Team->findById($teamId);
        if (empty($team)) {
            throw new InvalidArgumentException(sprintf('team not found on team id: %d', $teamId));
        }
        $paymentSetting = $this->PaymentSetting->getCcByTeamId($teamId);
        if (empty($paymentSetting)) {
            throw new InvalidArgumentException(sprintf('payment setting of credit card not found on team id: %d', $teamId));
        }

        // validate option: amount_charge_users
        $amountChargeUsers = $this->param('amount_charge_users');
        if (!AppUtil::isInt($amountChargeUsers)) {
            throw new InvalidArgumentException(sprintf('option amount_charge_users must be int: %s', $amountChargeUsers));
        }

        // validate option: target date time
        $targetDateTimeString = $this->param('target_date_time');
        if (is_null($targetDateTimeString)) {
            throw new InvalidArgumentException(sprintf('target_date_time must be date format'));
        }
        $targetDateTime = new GoalousDateTime($targetDateTimeString);

        return [
            $team['Team'],
            $paymentSetting,
            $amountChargeUsers,
            $targetDateTime,
        ];
    }

    public function main()
    {
        try {
            /**
             * @var $team array
             * @var $paymentSetting array
             * @var $amountChargeUsers int
             * @var $targetDateTime GoalousDateTime
             */
            list($team, $paymentSetting, $amountChargeUsers, $targetDateTime) = $this->getValuesFromOption();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return;
        }

        $this->hr();
        $this->out("Team Information");
        $this->hr();
        $this->logInfo(sprintf('Team          : %s', AppUtil::jsonOneLine($team)));
        $this->logInfo(sprintf('PaymentSetting: %s', AppUtil::jsonOneLine($paymentSetting)));
        $this->logInfo(sprintf('[option value] amount to charge users: %d', $amountChargeUsers));
        $this->logInfo(sprintf('[option value] target date time      : %s', $targetDateTime->format('Y-m-d H:i:s')));

        $currentCountTeamChargeMembers = $this->TeamMember->countChargeTargetUsers($team['id']);
        $this->logInfo(sprintf('current team member charge count(from db): %d', $currentCountTeamChargeMembers));

        $inputConfirmContinue = $this->in('Are you sure to continue credit card charge? [yes/no]');
        $this->logInfo(sprintf('confirm input: %s', $inputConfirmContinue));
        if ('yes' !== $inputConfirmContinue) {
            $this->out('aborted');
            return;
        }

        $this->hr();
        $this->out("Apply Credit card charge");
        $this->hr();
        try {
            $this->PaymentService->applyCreditCardCharge(
                $team['id'],
                Enum\ChargeHistory\ChargeType::MONTHLY_FEE(),
                $amountChargeUsers,
                null,
                $targetDateTime->getTimestamp()
            );
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            $this->logError($e->getTraceAsString());
        }
    }
}
