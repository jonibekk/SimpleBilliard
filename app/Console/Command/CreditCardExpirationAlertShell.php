<?php
App::uses('AppUtil', 'Util');
App::uses('AppController', 'Controller');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');
App::import('Service', 'CreditCardService');

/**
 * Class CreditCardExpirationAlertShell
 *
 * @property Team             $Team
 * @property TeamMember       $TeamMember
 * @property GlEmailComponent $GlEmail
 * @property PaymentSetting $PaymentSetting
 */
class CreditCardExpirationAlertShell extends AppShell
{
    public $uses = [
        'Team',
        'TeamMember',
        'PaymentSetting',
        'CreditCard'
    ];

    public function startup()
    {
        parent::startup();
        // initializing component
        $this->GlEmail = new GlEmailComponent(new ComponentCollection());
        $this->GlEmail->startup(new AppController());
    }

    /**
     * Entry point of the Shell
     */
    public function main()
    {
        // Get Customer list from stripe API
        /** @var CreditCardService $CreditCardService */
        $CreditCardService = ClassRegistry::init('CreditCardService');
        $customerList = $CreditCardService->listAllCustomers();
        if ($customerList['error']) {
            $this->log('Error retrieving Stripe customers', LOG_ERR);
            $this->log($customerList['message'], LOG_ERR);
            exit;
        }

        // Create a Key/Value of customers_id
        $stripeCustomers = array();
        foreach ($customerList['customers'] as $item) {
            $stripeCustomers[$item->id] = $item;
        }

        // Get teams only credit card payment type
        $targetChargeTeams = $this->PaymentSetting->findMonthlyChargeCcTeams();
        if (empty($targetChargeTeams)) {
            $this->log('Billing team does not exist', LOG_INFO);
            exit;
        }

        // Get current month and year
        $now = new DateTime('now');
        $month = $now->format('n');
        $year = $now->format('Y');

        foreach ($targetChargeTeams as $index => $teamInfo) {
            // Get customer data
            $custCode = $teamInfo['CreditCard']['customer_code'];
            $cardData = $stripeCustomers[$custCode];

            // Since we do not store the credit card ID on our database,
            // assume that the current registered credit card is the last
            // card on the list.
            $creditCardInfo = $cardData->sources->data[count($cardData->sources->data)-1];
            $expireYear = $creditCardInfo->exp_year;
            $expireMonth = $creditCardInfo->exp_month;

            // Cards that will expire this month
            if ($expireYear == $year && $expireMonth == $month) {
                $teamId = $teamInfo['PaymentSetting']['team_id'];
                $this->notifyExpiringCard($teamId, $creditCardInfo);
            }
        }
    }

    /**
     * Send card expiring notification email to Team admins
     *
     * @param int $teamId
     * @param     $cardData
     */
    private function notifyExpiringCard(int $teamId, $cardData)
    {
        // Validate card information
        if ($cardData == null || empty($cardData['last4']) || empty($cardData['brand'])) {
            $this->log("Invalid card data:", LOG_WARNING);
            $this->log(print_r($cardData, true), LOG_WARNING);
            return;
        }

        $lastDigits = $cardData->last4;
        $brand = $cardData->brand;

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $adminList = $TeamMember->findAdminList($teamId);
        if (!empty($adminList)) {
            // sending emails to each admins.
            foreach ($adminList as $toUid) {
                $this->GlEmail->sendMailCreditCardExpireAlert($toUid, $teamId, $brand, $lastDigits);
            }
        } else {
            $this->log("TeamId:{$teamId} There is no admin..", LOG_WARNING);
        }
    }
}