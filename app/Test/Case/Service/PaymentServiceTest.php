<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PaymentService');

/**
 * Class PaymentServiceTest
 *
 * @property PaymentService $PaymentService
 */
class PaymentServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.payment_setting',
        'app.payment_setting_change_log',
        'app.credit_card',
        'app.team',
        'app.team_member',
        'app.user'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->setDefaultTeamIdAndUid();
        $this->PaymentService = ClassRegistry::init('PaymentService');
    }

    public function test_validateCreate_validate()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => 'ISAO',
            'company_address' => 'Here Japan',
            'company_tel' => '123456789',
            'email' => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency' => 1
        ];

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertTrue($res);
    }

    public function test_validateCreate_validateError_token()
    {
        // No Token
        $payment = [
            'token'            => '',
            'team_id'          => 1,
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payer_name'       => 'Goalous Taro',
            'company_name'     => 'ISAO',
            'company_address'  => 'Here Japan',
            'company_tel'      => '123456789',
            'email'            => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency'         => 1
        ];
        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_teamId()
    {
        // No team ID
        $payment = [
            'token'            => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'type'             => 1,
            'amount_per_user'  => 1800,
            'payer_name'       => 'Goalous Taro',
            'company_name'     => 'ISAO',
            'company_address'  => 'Here Japan',
            'company_tel'      => '123456789',
            'email'            => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency'         => 1
        ];
        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_wrongType()
    {
        // Wrong type
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 3,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => 'ISAO',
            'company_address' => 'Here Japan',
            'company_tel' => '123456789',
            'email' => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency' => 1
        ];
        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_payerName()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => '',
            'company_name' => 'ISAO',
            'company_address' => 'Here Japan',
            'company_tel' => '123456789',
            'email' => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency' => 1
        ];

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_companyName()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => '',
            'company_address' => 'Here Japan',
            'company_tel' => '123456789',
            'email' => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency' => 1
        ];

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_Address()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => 'ISAO',
            'company_tel' => '123456789',
            'email' => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency' => 1
        ];

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_tel()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => 'ISAO',
            'company_address' => 'Here Japan',
            'company_tel' => '',
            'email' => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency' => 1
        ];

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_noEmail()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => 'ISAO',
            'company_address' => 'Here Japan',
            'company_tel' => '123456789',
            'email' => '',
            'payment_base_day' => 15,
            'currency' => 1
        ];

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_wrongEmail()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => 'ISAO',
            'company_address' => 'Here Japan',
            'company_tel' => '123456789',
            'email' => 'tesxxxxx.com',
            'payment_base_day' => 15,
            'currency' => 1
        ];

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_wrongPaymentDay()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => 'ISAO',
            'company_address' => 'Here Japan',
            'company_tel' => '123456789',
            'email' => 'test@goalous.com',
            'payment_base_day' => 33,
            'currency' => 1
        ];

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_validateCreate_validateError_wrongCurrency()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => 'ISAO',
            'company_address' => 'Here Japan',
            'company_tel' => '123456789',
            'email' => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency' => 12
        ];

        $res = $this->PaymentService->validateCreate($payment);
        $this->assertFalse($res === true);
    }

    public function test_createCreditCardPayment()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => 'ISAO',
            'company_address' => 'Here Japan',
            'company_tel' => '123456789',
            'email' => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency' => 1
        ];
        $customerCode = 'cus_B3ygr9hxqg5evH';

        $userID = $this->createActiveUser(1);
        $res = $this->PaymentService->createCreditCardPayment($payment, $customerCode, $userID);
        $this->assertTrue($res);
    }

    public function test_createCreditCardPayment_noCustomerCode()
    {
        $payment = [
            'token' => 'tok_1Ahqr1AM8AoVOHcFBeqD77cx',
            'team_id' => 1,
            'type' => 1,
            'amount_per_user' => 1800,
            'payer_name' => 'Goalous Taro',
            'company_name' => 'ISAO',
            'company_address' => 'Here Japan',
            'company_tel' => '123456789',
            'email' => 'test@goalous.com',
            'payment_base_day' => 15,
            'currency' => 1
        ];
        $customerCode = '';

        $userID = $this->createActiveUser(1);
        $res = $this->PaymentService->createCreditCardPayment($payment, $customerCode, $userID);
        $this->assertFalse($res);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PaymentService);
        parent::tearDown();
    }
}
