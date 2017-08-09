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
     * Generate a Token from Stripe API.
     * This method should not be used on production but only for test cases.
     * For production use stripe.js instead.
     *
     * @param string $cardNumber
     * @param string $cardHolder
     * @param int    $expireMonth
     * @param int    $expireYear
     * @param string $cvc
     *
     * @return array
     */
    public function createToken(string $cardNumber, string $cardHolder, int $expireMonth, int $expireYear, string $cvc): array
    {
        $result = [
            "error" => false,
            "message" => null
        ];

        $request = array(
            "card" => array(
                "number" => $cardNumber,
                "exp_month" => $expireMonth,
                "exp_year" => $expireYear,
                "cvc" => $cvc,
                "name" => $cardHolder
            )
        );

        // Use public key to create token
        \Stripe\Stripe::setApiKey(STRIPE_PUBLISHABLE_KEY);

        try {
            $response = \Stripe\Token::create($request);

            $result["token"] = $response->id;
        }
        catch (Exception $e) {
            $result["error"] = true;
            $result["message"] = $e->getMessage();

            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
        }

        return $result;
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
        $res = $this->createToken($cardNumber, "Goalous Taro", 11, 2026, "123");

        $this->assertNotNull($res);
        $this->assertArrayHasKey("token", $res);
        $this->assertArrayHasKey("error", $res);
        $this->assertFalse($res["error"]);

        return $res;
    }

    /**
     * Get a customer for a given credit card.
     * @param string $creditCard
     *
     * @return string
     */
    private function getCustomer(string $creditCard): string
    {
        $token = $this->getToken($creditCard);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token["token"], $email, "Goalous TEST");
        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayHasKey("customer_id", $res);
        $this->assertArrayHasKey("card", $res);

        return $res["customer_id"];
    }

    /**
     * Delete Stripe Customer
     * 
     * @param $customerId
     */
    private function deleteCustomer($customerId)
    {
        $res = $this->CreditCardService->deleteCustomer($customerId);

        $this->assertNotNull($res);
        $this->assertArrayHasKey("error", $res);
        $this->assertArrayHasKey("deleted", $res);
        $this->assertFalse($res["error"]);
        $this->assertTrue($res["deleted"]);
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
        $token = $this->getToken(self::CARD_VISA);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token["token"], $email, "Goalous TEST");

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
        $token = $this->getToken(self::CARD_DECLINED);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token["token"], $email, "Goalous TEST");

        $this->assertErrorCard($res, "card_declined");
    }

    /**
     * Assert it can handle incorrect card verification
     */
    function test_registerCustomer_incorrectCVC()
    {
        $token = $this->getToken(self::CARD_INCORRECT_CVC);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token["token"], $email, "Goalous TEST");

        $this->assertErrorCard($res, "incorrect_cvc");
    }

    /**
     * Assert it can handle expired card
     */
    function test_registerCustomer_cardExpired()
    {
        $token = $this->getToken(self::CARD_EXPIRED);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token["token"], $email, "Goalous TEST");

        $this->assertErrorCard($res, "expired_card");
    }

    /**
     * Assert it can handle error on card processing
     */
    function test_registerCustomer_processingError()
    {
        $token = $this->getToken(self::CARD_PROCESSING_ERROR);
        $email = "test@goalous.com";

        $res = $this->CreditCardService->registerCustomer($token["token"], $email, "Goalous TEST");

        $this->assertErrorCard($res, "processing_error");
    }

    /**
     * Assert it can charge the customer with valid credit card.
     */
    function test_chargeCustomer()
    {
        $customerId = $this->getCustomer(self::CARD_VISA);

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
        $customerId = $this->getCustomer(self::CARD_CHARGE_FAIL);

        $res = $this->CreditCardService->chargeCustomer($customerId, 'JPY', 30000, "Test charge ¥3000");

        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayHasKey("error", $res);
        $this->assertTrue($res["error"]);

        $this->deleteCustomer($customerId);
    }

    /**
     * Assert a list have been returned.
     */
    function test_listAllCustomers()
    {
        $res = $this->CreditCardService->listAllCustomers();

        $this->assertNotNull($res, "Something very wrong happened");
        $this->assertArrayHasKey("error", $res);
        $this->assertFalse($res["error"]);
        $this->assertArrayHasKey("customers", $res);
    }
}
