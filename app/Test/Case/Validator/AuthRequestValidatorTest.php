<?php
App::uses('GoalousTestCase', 'Test');
App::uses('AuthRequestValidator', 'Validator/Request/Api/V2');

class AuthRequestValidatorTest extends GoalousTestCase
{
    const CHARS_09 = '0123456789';
    const CHARS_az = 'abcdefghijklmnopqrstuvwxyz';
    const CHARS_AZ = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const CHARS_PASSWORD_PERMIT_SYMBOL = '!@#$%^&*()_-+={}[]|:;<>,.?/';

    public function dataProviderLoginSuccess()
    {
        $userEmail = "user@example.com";

        yield [$userEmail, "0a"];
        yield [$userEmail, str_pad("", 1024,
            self::CHARS_09 . self::CHARS_az . self::CHARS_AZ . self::CHARS_PASSWORD_PERMIT_SYMBOL
            )];

        yield [$userEmail, self::CHARS_09 . self::CHARS_az];
        yield [$userEmail, self::CHARS_az . self::CHARS_09];
        yield [$userEmail, self::CHARS_09 . self::CHARS_AZ];
        yield [$userEmail, self::CHARS_AZ . self::CHARS_09];
        yield ["user+abcde@example.com", "0a"];
    }

    /**
     * @dataProvider dataProviderLoginSuccess
     * @param string $username
     * @param string $password
     */
    public function test_validatePost_success(string $username, string $password)
    {
        $authRequestValidator = AuthRequestValidator::createLoginValidator();
        $this->assertTrue($authRequestValidator->validate([
            'username' => $username,
            'password' => $password
        ]));
    }

    public function dataProviderLoginFail()
    {
        $defaultUserEmail = "user@example.com";
        $defaultUserPassword = "0a";

        // Failing email
        yield [null, $defaultUserPassword];
        yield ['', $defaultUserPassword];
        yield ['not_email_string', $defaultUserPassword];

        // TODO: Move this password regex test to the User Registration Request Test
        // Failing password
//        yield [$defaultUserEmail, null];
//        yield [$defaultUserEmail, ''];
//        yield [$defaultUserEmail, self::CHARS_09];
//        yield [$defaultUserEmail, self::CHARS_az];
//        yield [$defaultUserEmail, self::CHARS_AZ];
//        yield [$defaultUserEmail, self::CHARS_az . self::CHARS_AZ];
//        yield [$defaultUserEmail, self::CHARS_PASSWORD_PERMIT_SYMBOL];
//        yield [$defaultUserEmail, self::CHARS_09 . self::CHARS_PASSWORD_PERMIT_SYMBOL];
//        yield [$defaultUserEmail, self::CHARS_az . self::CHARS_PASSWORD_PERMIT_SYMBOL];
//        yield [$defaultUserEmail, self::CHARS_AZ . self::CHARS_PASSWORD_PERMIT_SYMBOL];
//        yield [$defaultUserEmail, self::CHARS_az . self::CHARS_AZ . self::CHARS_PASSWORD_PERMIT_SYMBOL];
//        yield [$defaultUserEmail, $defaultUserPassword . '~']; // added not permitted symbol
//        yield [$defaultUserEmail, "\n" . $defaultUserPassword . "\n"];
//
//        $escapeSequences = [" ", "\0", "\a", "\b", "\t", "\n", "\v", "\f", "\r", "\e"];
//        foreach ($escapeSequences as $es) {
//            yield [$defaultUserEmail, $es . $defaultUserPassword];
//            yield [$defaultUserEmail, $defaultUserPassword . $es];
//            yield [$defaultUserEmail, $es . $defaultUserPassword . $es];
//            yield [$defaultUserEmail, $es . $defaultUserPassword . $es . $defaultUserPassword . $es];
//        }
    }

    /**
     * @dataProvider dataProviderLoginFail
     * @param string|null $username
     * @param string|null $password
     *
     * @expectedException Respect\Validation\Exceptions\AllOfException
     */
    public function test_validatePost_failPassword($username, $password)
    {
        $authRequestValidator = AuthRequestValidator::createLoginValidator();
        $authRequestValidator->validate([
            'username' => $username,
            'password' => $password
        ]);

        $this->fail("Does not threw exception on:" . json_encode([$username, $password]));
    }
}
