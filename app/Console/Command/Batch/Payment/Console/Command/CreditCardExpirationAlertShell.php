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
    protected $enableOutputLogStartStop = true;

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
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init('CreditCard');
        $lastCustomer = null;

        do {
            // Call list customer API
            $respListCustomer = $CreditCardService->listCustomers($lastCustomer);
            if ($respListCustomer['error']) {
                $this->logError('Error retrieving Stripe customers: '.$respListCustomer['message']);
                exit;
            }

            // Get list of customer ids
            $customerCodeList = array_keys($respListCustomer['customers']);
            // Match the list on database
            $registeredCards = $CreditCard->findByCustomerCodes($customerCodeList);

            // Check for expiring cards
            $this->_checkExpiringCards($registeredCards, $respListCustomer['customers']);

            // If has more set the last Customer id
            if ($respListCustomer['hasMore']) {
                $this->logInfo('has more customers, fetch more customers from stripe');
                $lastCustomer = $customerCodeList[count($customerCodeList)-1];
            }
        } while($respListCustomer['hasMore']);
    }

    /**
     * Check for expiring credit cards on customer list
     *
     * Customer object description can be found on Slack documentation
     * https://stripe.com/docs/api#customer_object
     *
     * @param array $registeredCards - List of credit card registered on Goalous
     * @param array $customerList    - List of Customers registerd on Slack
     */
    private function _checkExpiringCards(array $registeredCards, array $customerList)
    {
        // Get current month and year
        $now = GoalousDateTime::now();
        $currentMonth = $now->format('n');
        $currentYear = $now->format('Y');

        foreach ($registeredCards as $creditCard)
        {
            $customerData = $customerList[Hash::get($creditCard, 'CreditCard.customer_code')];

            // Get the default credit card
            $creditCardInfo = null;
            if (count($customerData->sources->data) == 1) {
                // Single card - this is the default
                $creditCardInfo = $customerData->sources->data[0];
            } else {
                // Look for the default card on the list
                foreach ($customerData->sources->data as $source) {
                    if ($source->id == $customerData->default_source) {
                        $creditCardInfo = $source;
                    }
                }
                // This is a non expected case.
                // The default card should exists
                if ($creditCardInfo == null) {
                    $this->logError('Customer without default credit card: '.$customerData->id);
                    continue;
                }
            }

            $expireYear = $creditCardInfo->exp_year;
            $expireMonth = $creditCardInfo->exp_month;

            $this->logInfo(sprintf('compare credit card expiration with team: %s', AppUtil::varExportOneLine([
                'card.exp_year'  => $expireYear,
                'card.exp_month' => $expireMonth,
            ])));

            // Cards that will expire this month
            if ($expireYear == $currentYear && $expireMonth == $currentMonth) {
                $teamId = Hash::get($creditCard, 'CreditCard.team_id');
                $this->_notifyExpiringCard($teamId, $creditCardInfo);
            }

            // Remove processed from the list
            unset($customerList[Hash::get($creditCard, 'CreditCard.customer_code')]);
        }

        // Remaining customers on the list do not have a match on Goalous database
        // Check if are not test accounts and log otherwise
        foreach ($customerList as $customerData) {
            if (strpos($customerData->description, 'TEST') !== false) {
                continue;
            }
            // Log the customer data for later check
            $this->logError('Customer without a match on Goalous database. customerId: '.$customerData->id);
        }
    }

    /**
     * Send card expiring notification email to Team admins
     *
     * @param int $teamId
     * @param     $cardData
     */
    private function _notifyExpiringCard(int $teamId, $cardData)
    {
        // Validate card information
        if ($cardData == null || empty($cardData['last4']) || empty($cardData['brand'])) {
            $this->logError("Invalid card data. team id: ". $teamId);
            return;
        }
        $this->logInfo(sprintf('notify credit card expiration to team: %s', AppUtil::varExportOneLine([
            'teams.id' => $teamId,
        ])));

        $lastDigits = $cardData->last4;
        $brand = $cardData->brand;

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');
        $adminList = $TeamMember->findAdminList($teamId);
        if (!empty($adminList)) {
            $team = $this->Team->getById($teamId);
            // sending emails to each admins.
            foreach ($adminList as $toUid) {
                $this->GlEmail->sendMailCreditCardExpireAlert($toUid, $teamId, $brand, $lastDigits, $team['name']);
            }
        } else {
            $this->logError("This team have no admin: $teamId");
        }
    }
}