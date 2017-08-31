<?php
App::uses('GoalousTestCase', 'Test');
App::uses('GoalousDateTime', 'DateTime');
App::import('Service', 'CreditCardService');

/**
 * Class CreditCardServiceTest
 *
 * @property CreditCardService $CreditCardService
 */
class CreditCardServiceTest extends GoalousTestCase
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

        $res = $this->CreditCardService->updateCreditCard($customerId, $newCardToken, 3);
        $this->assertFalse($res['error']);

        $this->deleteCustomer($customerId);
    }

    function test_update_invalidToken()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);

        $res = $this->CreditCardService->updateCreditCard($customerId, 'xxxxxxxxx', 3);
        $this->assertTrue($res['error']);

        $this->deleteCustomer($customerId);
    }

    function test_update_cardDeclined()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);
        $newCardToken = $this->createToken(self::CARD_DECLINED);

        $res = $this->CreditCardService->updateCreditCard($customerId, $newCardToken, 3);
        $this->assertTrue($res['error']);
        $this->assertEqual($res['errorCode'], self::ERR_CODE_CARD_DECLINED);

        $this->deleteCustomer($customerId);
    }

    function test_update_incorrectCVC()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);
        $newCardToken = $this->createToken(self::CARD_INCORRECT_CVC);

        $res = $this->CreditCardService->updateCreditCard($customerId, $newCardToken, 3);
        $this->assertTrue($res['error']);
        $this->assertEqual($res['errorCode'], self::ERR_CODE_CARD_INCORRECT_CVC);

        $this->deleteCustomer($customerId);
    }

    function test_update_cardExpired()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);
        $newCardToken = $this->createToken(self::CARD_EXPIRED);

        $res = $this->CreditCardService->updateCreditCard($customerId, $newCardToken, 3);
        $this->assertTrue($res['error']);
        $this->assertEqual($res['errorCode'], self::ERR_CODE_CARD_EXPIRED);

        $this->deleteCustomer($customerId);
    }

    function test_update_processingError()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);
        $newCardToken = $this->createToken(self::CARD_PROCESSING_ERROR);

        $res = $this->CreditCardService->updateCreditCard($customerId, $newCardToken, 3);
        $this->assertTrue($res['error']);
        $this->assertEqual($res['errorCode'], self::ERR_CODE_CARD_PROCESSING_ERROR);

        $this->deleteCustomer($customerId);
    }

    function test_retrieveCustomer()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);

        $res = $this->CreditCardService->retrieveCustomer($customerId);
        $this->assertFalse($res["error"]);
        $this->assertArrayHasKey("customer", $res);

        $this->deleteCustomer($customerId);
    }

    function test_retrieveCustomer_invalidId()
    {
        $customerId = 'xxxxxx';

        $res = $this->CreditCardService->retrieveCustomer($customerId);
        $this->assertTrue($res["error"] === true);
    }

    function test_retrieveCreditCard()
    {
        $customerId = $this->createCustomer(self::CARD_VISA);

        $res = $this->CreditCardService->retrieveCreditCard($customerId);
        $this->assertFalse($res["error"]);
        $this->assertArrayHasKey("creditCard", $res);

        $this->deleteCustomer($customerId);
    }

    function test_retrieveCreditCard_invalidId()
    {
        $customerId = 'xxxxxx';

        $res = $this->CreditCardService->retrieveCreditCard($customerId);
        $this->assertTrue($res["error"] === true);
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

    function test_cacheTeamCreditCardExpiration()
    {
        $testingTeamId = 3;
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        $CreditCard->current_team_id = $testingTeamId;
        $this->CreditCardService->cacheTeamCreditCardExpiration([
            'error' => false,
            'year'  => 2018,
            'month' => 8,
        ], $testingTeamId);

        $key = $CreditCard->getCacheKey(CACHE_KEY_TEAM_CREDIT_CARD_EXPIRE_DATE, false, null, true);
        $cachedCreditCardExpireData = Cache::read($key, 'user_data');
        $creditCardExpireData = msgpack_unpack($cachedCreditCardExpireData);
        $this->assertFalse($creditCardExpireData['error']);
        $this->assertEquals(2018, $creditCardExpireData['year']);
        $this->assertEquals(8, $creditCardExpireData['month']);

        $customerId = $this->createCustomer(self::CARD_VISA);
        $newCardToken = $this->createToken(self::CARD_MASTERCARD);
        $this->CreditCardService->updateCreditCard($customerId, $newCardToken, $testingTeamId);
        $this->assertFalse(Cache::read($key, 'user_data'));
    }

    function test_getRealExpireDateTimeFromCreditCardExpireDate()
    {
        $expireDate = $this->CreditCardService->getRealExpireDateTimeFromCreditCardExpireDate(2018, 9);
        self::assertTrue(
            $expireDate->equalTo(new GoalousDateTime('2018-10-01 00:00:00'))
        );
        $expireDate = $this->CreditCardService->getRealExpireDateTimeFromCreditCardExpireDate(2018, 12);
        self::assertTrue(
            $expireDate->equalTo(new GoalousDateTime('2019-01-01 00:00:00'))
        );
    }

    function test_getExpirationDateTimeOfTeamCreditCardFromCache()
    {
        $testingTeamId = 3;
        /** @var CreditCard $CreditCard */
        $CreditCard = ClassRegistry::init("CreditCard");
        $CreditCard->current_team_id = $testingTeamId;
        $this->CreditCardService->cacheTeamCreditCardExpiration([
            'error' => false,
            'year'  => 2019,
            'month' => 8,
        ], $testingTeamId);
        $d = $this->CreditCardService->getExpirationDateTimeOfTeamCreditCardFromCache($testingTeamId);

        $this->assertTrue(
            (new GoalousDateTime('2019-09-01 00:00:00'))->equalTo($d)
        );
    }
}
