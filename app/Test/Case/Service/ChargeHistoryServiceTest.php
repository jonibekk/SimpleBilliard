<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'ChargeHistoryService');
App::import('DateTime', 'GoalousDateTime');

use Goalous\Model\Enum as Enum;

/**
 * Class ChargeHistoryService
 *
 * @property ChargeHistoryService $ChargeHistoryService
 */
class ChargeHistoryServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.payment_setting',
        'app.credit_card',
        'app.charge_history',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ChargeHistoryService = ClassRegistry::init('ChargeHistoryService');
        $this->PaymentSetting = ClassRegistry::init('PaymentSetting');
        $this->ChargeHistory = ClassRegistry::init('ChargeHistory');
        $this->CreditCard = ClassRegistry::init('CreditCard');
    }

    public function test_isLatestChargeSucceed()
    {
        $this->PaymentSetting->save([
            'id'                             => 1,
            'team_id'                        => 1,
            'type'                           => Enum\PaymentSetting\Type::CREDIT_CARD,
            'currency'                       => Enum\PaymentSetting\Currency::JPY,
            'amount_per_user'                => 1980,
            'company_name'                   => 'TestCompany Ltd.',
            'company_country'                => '',
            'company_post_code'              => '',
            'company_region'                 => '',
            'company_city'                   => '',
            'company_street'                 => '',
            'company_tel'                    => '',
            'contact_person_first_name'      => '',
            'contact_person_first_name_kana' => '',
            'contact_person_last_name'       => '',
            'contact_person_last_name_kana'  => '',
            'contact_person_tel'             => '',
            'contact_person_email'           => '',
            'payment_base_day'               => 1500000000,
            'del_flg'                        => 0,
            'deleted'                        => null,
            'created'                        => 1500000000,
            'modified'                       => 1500000000,
        ], false);
        $this->PaymentSetting->save([
            'id'                             => 2,
            'team_id'                        => 2,
            'type'                           => Enum\PaymentSetting\Type::CREDIT_CARD,
            'currency'                       => Enum\PaymentSetting\Currency::JPY,
            'amount_per_user'                => 1980,
            'company_name'                   => 'TestCompany Team2 Ltd.',
            'company_country'                => '',
            'company_post_code'              => '',
            'company_region'                 => '',
            'company_city'                   => '',
            'company_street'                 => '',
            'company_tel'                    => '',
            'contact_person_first_name'      => '',
            'contact_person_first_name_kana' => '',
            'contact_person_last_name'       => '',
            'contact_person_last_name_kana'  => '',
            'contact_person_tel'             => '',
            'contact_person_email'           => '',
            'payment_base_day'               => 1500000000,
            'del_flg'                        => 0,
            'deleted'                        => null,
            'created'                        => 1500000000,
            'modified'                       => 1500000000,
        ], false);
        $this->PaymentSetting->save([
            'id'                             => 3,
            'team_id'                        => 3,
            'type'                           => Enum\PaymentSetting\Type::INVOICE,
            'currency'                       => Enum\PaymentSetting\Currency::JPY,
            'amount_per_user'                => 1980,
            'company_name'                   => 'TestCompany Team3 Ltd.',
            'company_country'                => '',
            'company_post_code'              => '',
            'company_region'                 => '',
            'company_city'                   => '',
            'company_street'                 => '',
            'company_tel'                    => '',
            'contact_person_first_name'      => '',
            'contact_person_first_name_kana' => '',
            'contact_person_last_name'       => '',
            'contact_person_last_name_kana'  => '',
            'contact_person_tel'             => '',
            'contact_person_email'           => '',
            'payment_base_day'               => (new GoalousDateTime('2017-08-28 00:00:00'))->getTimestamp(),
            'del_flg'                        => 0,
            'deleted'                        => null,
            'created'                        => (new GoalousDateTime('2017-08-28 00:00:00'))->getTimestamp(),
            'modified'                       => (new GoalousDateTime('2017-08-28 00:00:00'))->getTimestamp(),
        ], false);

        $this->ChargeHistory->save([
            'id'                  => 1,
            'team_id'             => 1,
            'user_id'             => 1,
            'payment_type'        => Enum\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'         => Enum\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'     => 1980,
            'total_amount'        => 1980,
            'tax'                 => 0,
            'charge_users'        => 1,
            'currency'            => Enum\PaymentSetting\Currency::JPY,
            'charge_datetime'     => 1500000000,
            'result_type'         => Enum\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users'    => 1,
            'stripe_payment_code' => '',
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1500000000,
            'modified'            => 1500000000,
        ], false);
        // for team id 2
        $this->ChargeHistory->save([
            'id'                  => 2,
            'team_id'             => 2,
            'user_id'             => 2,
            'payment_type'        => Enum\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'         => Enum\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'     => 1980,
            'total_amount'        => 1980,
            'tax'                 => 0,
            'charge_users'        => 1,
            'currency'            => Enum\PaymentSetting\Currency::JPY,
            'charge_datetime'     => 1500000001,
            'result_type'         => Enum\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users'    => 1,
            'stripe_payment_code' => '',
            'del_flg'             => 1,
            'deleted'             => 1500000000,
            'created'             => 1500000000,
            'modified'            => 1500000000,
        ], false);
        $this->ChargeHistory->save([
            'id'                  => 3,
            'team_id'             => 2,
            'user_id'             => 2,
            'payment_type'        => Enum\PaymentSetting\Type::CREDIT_CARD,
            'charge_type'         => Enum\ChargeHistory\ChargeType::MONTHLY_FEE,
            'amount_per_user'     => 1980,
            'total_amount'        => 1980,
            'tax'                 => 0,
            'charge_users'        => 1,
            'currency'            => Enum\PaymentSetting\Currency::JPY,
            'charge_datetime'     => 1500000001,
            'result_type'         => Enum\ChargeHistory\ResultType::FAIL,
            'max_charge_users'    => 1,
            'stripe_payment_code' => '',
            'del_flg'             => 0,
            'deleted'             => null,
            'created'             => 1500000001,
            'modified'            => 1500000001,
        ], false);
        $this->CreditCard->save([
            'id'                 => 1,
            'team_id'            => 1,
            'payment_setting_id' => 1,
            'customer_code'      => 'cus_XXXXXXXXXX',
            'del_flg'            => 0,
            'deleted'            => null,
            'created'            => 1500000000,
            'modified'           => 1500000000,
        ], false);
            $this->CreditCard->save([
            'id'                 => 2,
            'team_id'            => 2,
            'payment_setting_id' => 2,
            'customer_code'      => 'cus_XXXXXXXXXX',
            'del_flg'            => 0,
            'deleted'            => null,
            'created'            => 1500000000,
            'modified'           => 1500000000,
        ], false);

        // last payment succeeded
        $this->assertFalse($this->ChargeHistoryService->isLatestChargeFailed(1));
        // failed last payment
        $this->assertTrue($this->ChargeHistoryService->isLatestChargeFailed(2));
        // setting is invoice
        $this->assertFalse($this->ChargeHistoryService->isLatestChargeFailed(3));
    }
}
