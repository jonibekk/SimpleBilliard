<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'CreditCardService');

/**
 * Class CreditCardServiceTest
 *
 * @property CreditCardService $CreditCardService
 */
class CreditCardServiceTest extends GoalousTestCase
{
    // Card with specific error for Stripe API test
    // https://stripe.com/docs/testing#cards-responses
    // Error Cards
    const CARD_DECLINED = "4000000000000002";
    const CARD_INCORRECT_CVC = "4000000000000127";
    const CARD_EXPIRED = "4000000000000069";
    const CARD_PROCESSING_ERROR = "4000000000000119";
    const CARD_INCORRECT_NUMBER = "4242424242424241";
    const CARD_CHARGE_FAIL = "4000000000000341";
    // Valid Cards
    const CARD_VISA = "4012888888881881";
    const CARD_MASTERCARD = "5555555555554444";

    const ERR_CODE_CARD_DECLINED = 'card_declined';
    const ERR_CODE_CARD_INCORRECT_CVC = "incorrect_cvc";
    const ERR_CODE_CARD_EXPIRED = 'expired_card';
    const ERR_CODE_CARD_PROCESSING_ERROR = 'processing_error';

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->CreditCardService = ClassRegistry::init('CreditCardService');
    }

    /**
     * Common assertion for bad credit cards
     *
     * @param array  $apiResponse
     * @param string $errorCode
     */
    private function assertErrorCard(array $apiResponse, string $errorCode)
    {
        $this->assertNotNull($apiResponse, "Something very wrong happened");
        $this->assertTrue($apiResponse["error"]);
        $this->assertArrayNotHasKey("customer_id", $apiResponse);
        $this->assertArrayHasKey("errorCode", $apiResponse);
        $this->assertEquals($errorCode, $apiResponse["errorCode"]);
    }

    /**
     * Test new customer registration
     */
    function test_registerCustomer()
    {
        $token = $this->createToken(self::CARD_VISA);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token, $email, "Goalous TEST");

        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayHasKey("customer_id", $res);
        $this->assertArrayHasKey("card", $res);

        $this->deleteCustomer($res["customer_id"]);
    }

    /**
     * Assert it can handle wrong tokens
     */
    function test_registerCustomer_invalidToken()
    {
        $email = "test@goalous.com";
        $token = "xxxxxxxxxxxx";

        $res = $this->CreditCardService->registerCustomer($email, $token, "Goalous TEST");

        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayNotHasKey("customer_id", $res);
        $this->assertTrue($res["error"]);
    }

    /**
     * Assert it can handle declined cards
     */
    function test_registerCustomer_cardDeclined()
    {
        $token = $this->createToken(self::CARD_DECLINED);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token, $email, "Goalous TEST");

        $this->assertErrorCard($res, "card_declined");
    }

    /**
     * Assert it can handle incorrect card verification
     */
    function test_registerCustomer_incorrectCVC()
    {
        $token = $this->createToken(self::CARD_INCORRECT_CVC);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token, $email, "Goalous TEST");

        $this->assertErrorCard($res, "incorrect_cvc");
    }

    /**
     * Assert it can handle expired card
     */
    function test_registerCustomer_cardExpired()
    {
        $token = $this->createToken(self::CARD_EXPIRED);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token, $email, "Goalous TEST");

        $this->assertErrorCard($res, "expired_card");
    }

    /**
     * Assert it can handle error on card processing
     */
    function test_registerCustomer_processingError()
    {
        $token = $this->createToken(self::CARD_PROCESSING_ERROR);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token, $email, "Goalous TEST");

        $this->assertErrorCard($res, "processing_error");
    }

    /**
     * Assert it can charge the customer with valid credit card.
     */
    function test_chargeCustomer()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);

        $res = $this->CreditCardService->chargeCustomer($customerId, 'JPY', 30000, "Test charge ¥3000");

        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayHasKey("success", $res);
        $this->assertTrue($res["success"]);

        $this->deleteCustomer($customerId);
    }

    /**
     * Assert an error is returned if the credit is rejected.
     */
    function test_chargeCustomer_chargeFail()
    {
        $customerId = $this->createCustomer(self::CARD_CHARGE_FAIL);

        $res = $this->CreditCardService->chargeCustomer($customerId, 'JPY', 30000, "Test charge ¥3000");

        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayHasKey("error", $res);
        $this->assertTrue($res["error"]);

        $this->deleteCustomer($customerId);
    }

    function test_update()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);
        $newCardToken = $this->createToken(self::CARD_MASTERCARD);

        $res = $this->CreditCardService->update($customerId, $newCardToken);
        $this->assertFalse($res['error']);
    }

    function test_update_invalidToken()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);

        $res = $this->CreditCardService->update($customerId, 'xxxxxxxxx');
        $this->assertTrue($res['error']);
    }

    function test_update_cardDeclined()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);
        $newCardToken = $this->createToken(self::CARD_DECLINED);

        $res = $this->CreditCardService->update($customerId, $newCardToken);
        $this->assertTrue($res['error']);
        $this->assertEqual($res['errorCode'], self::ERR_CODE_CARD_DECLINED);
    }

    function test_update_incorrectCVC()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);
        $newCardToken = $this->createToken(self::CARD_INCORRECT_CVC);

        $res = $this->CreditCardService->update($customerId, $newCardToken);
        $this->assertTrue($res['error']);
        $this->assertEqual($res['errorCode'], self::ERR_CODE_CARD_INCORRECT_CVC);
    }

    function test_update_cardExpired()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);
        $newCardToken = $this->createToken(self::CARD_EXPIRED);

        $res = $this->CreditCardService->update($customerId, $newCardToken);
        $this->assertTrue($res['error']);
        $this->assertEqual($res['errorCode'], self::ERR_CODE_CARD_EXPIRED);
    }

    function test_update_processingError()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);
        $newCardToken = $this->createToken(self::CARD_PROCESSING_ERROR);

        $res = $this->CreditCardService->update($customerId, $newCardToken);
        $this->assertTrue($res['error']);
        $this->assertEqual($res['errorCode'], self::ERR_CODE_CARD_PROCESSING_ERROR);
    }
    
    /**
     * Assert a list have been returned.
     */
    function test_listAllCustomers()
    {
        $res = $this->CreditCardService->listCustomers();

        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayHasKey("error", $res);
        $this->assertFalse($res["error"]);
        $this->assertArrayHasKey("customers", $res);
    }
}
