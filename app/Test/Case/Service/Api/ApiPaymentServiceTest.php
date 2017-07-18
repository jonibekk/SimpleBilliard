<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Api', 'ApiPaymentService');

/**
 * Class ApiPaymentServiceTest
 */
class ApiPaymentServiceTest extends GoalousTestCase
{
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
     * Get a new token for the given credit card.
     *
     * @param string $cardNumber
     *
     * @return array
     */
    private function getToken(string $cardNumber): array
    {
        $res = $this->ApiPaymentService->createToken($cardNumber, "Goalous Taro", 11, 2026, "123");

        $this->assertNotNull($res);
        $this->assertArrayHasKey("token", $res);

        return $res;
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
     * Test Token generation
     */
    function test_createToken()
    {
        $res = $this->ApiPaymentService->createToken("4012888888881881", "Goalous Taro", 11, 2026, "123");

        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayHasKey("error", $res);
        $this->assertArrayHasKey("token", $res);
        $this->assertFalse($res["error"]);
    }

    /**
     * Test new customer registration
     */
    function test_registerCustomer()
    {
        $token = $this->getToken("4012888888881881");
        $email = "test@goalous.com";

        $res = $this->ApiPaymentService->registerCustomer($token["token"], $email, "Goalous TEST");

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
        $token = $this->getToken("4000000000000002");
        $email = "test@goalous.com";

        $res = $this->ApiPaymentService->registerCustomer($token["token"], $email, "Goalous TEST");

        $this->assertErrorCard($res, "card_declined");
    }

    /**
     * Assert it can handle incorrect card verification
     */
    function test_registerCustomer_incorrectCVC()
    {
        $token = $this->getToken("4000000000000127");
        $email = "test@goalous.com";

        $res = $this->ApiPaymentService->registerCustomer($token["token"], $email, "Goalous TEST");

        $this->assertErrorCard($res, "incorrect_cvc");
    }

    /**
     * Assert it can handle expired card
     */
    function test_registerCustomer_cardExpired()
    {
        $token = $this->getToken("4000000000000069");
        $email = "test@goalous.com";

        $res = $this->ApiPaymentService->registerCustomer($token["token"], $email, "Goalous TEST");

        $this->assertErrorCard($res, "expired_card");
    }

    /**
     * Assert it can handle error on card processing
     */
    function test_registerCustomer_processingError()
    {
        $token = $this->getToken("4000000000000119");
        $email = "test@goalous.com";

        $res = $this->ApiPaymentService->registerCustomer($token["token"], $email, "Goalous TEST");

        $this->assertErrorCard($res, "processing_error");
    }

    /**
     * Assert it can handle incorrect card number
     */
    function test_registerCustomer_incorrectCardNumber()
    {
        $token = $this->ApiPaymentService->createToken("4242424242424241", "Goalous Taro", 11, 2026, "123");

        $this->assertNotNull($token, "Something very wrong happened");
        $this->assertArrayHasKey("error", $token);
        $this->assertArrayNotHasKey("token", $token);
        $this->assertTrue($token["error"]);
    }
}
