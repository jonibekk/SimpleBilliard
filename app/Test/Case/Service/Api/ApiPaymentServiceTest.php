<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Api', 'ApiPaymentService');

/**
 * Class ApiPaymentServiceTest
 */
class ApiPaymentServiceTest extends GoalousTestCase
{
    // Card with specific error for Stripe API test
    // https://stripe.com/docs/testing#cards-responses
    // Error Cards
    const CARD_DECLINED = "4000000000000002";
    const CARD_INCORRECT_CVC = "4000000000000127";
    const CARD_EXPIRED = "4000000000000069";
    const CARD_PROCESSING_ERROR = "4000000000000119";
    const CARD_INCORRECT_NUMBER = "4242424242424241";
    // Valid Cards
    const CARD_VISA = "4012888888881881";
    const CARD_MASTERCARD = "5555555555554444";

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

        $this->ApiPaymentService = ClassRegistry::init('ApiPaymentService');
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

        $res = $this->ApiPaymentService->registerCustomer($token, $email, "Goalous TEST");

        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayHasKey("customer_id", $res);
        $this->assertArrayHasKey("card", $res);
    }

    /**
     * Assert it can handle wrong tokens
     */
    function test_registerCustomer_invalidToken()
    {
        $email = "test@goalous.com";
        $token = "xxxxxxxxxxxx";

        $res = $this->ApiPaymentService->registerCustomer($email, $token, "Goalous TEST");

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

        $res = $this->ApiPaymentService->registerCustomer($token, $email, "Goalous TEST");

        $this->assertErrorCard($res, "card_declined");
    }

    /**
     * Assert it can handle incorrect card verification
     */
    function test_registerCustomer_incorrectCVC()
    {
        $token = $this->createToken(self::CARD_INCORRECT_CVC);
        $email = "test@goalous.com";

        $res = $this->ApiPaymentService->registerCustomer($token, $email, "Goalous TEST");

        $this->assertErrorCard($res, "incorrect_cvc");
    }

    /**
     * Assert it can handle expired card
     */
    function test_registerCustomer_cardExpired()
    {
        $token = $this->createToken(self::CARD_EXPIRED);
        $email = "test@goalous.com";

        $res = $this->ApiPaymentService->registerCustomer($token, $email, "Goalous TEST");

        $this->assertErrorCard($res, "expired_card");
    }

    /**
     * Assert it can handle error on card processing
     */
    function test_registerCustomer_processingError()
    {
        $token = $this->createToken(self::CARD_PROCESSING_ERROR);
        $email = "test@goalous.com";

        $res = $this->ApiPaymentService->registerCustomer($token, $email, "Goalous TEST");

        $this->assertErrorCard($res, "processing_error");
    }
}
