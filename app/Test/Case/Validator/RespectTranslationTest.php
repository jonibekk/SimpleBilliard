<?php
App::uses('GoalousTestCase', 'Test');
App::uses('User', 'Model');
App::uses('UserValidator', 'Validator/Model');
App::uses('AuthRequestValidator', 'Validator/Request/Api/V2');

use Respect\Validation\Validator as validator;

class RespectTranslationTest extends GoalousTestCase
{
    /**
     * Test for validation translate message
     *
     * @throws Exception
     */
    public function test_validatePost_success_en()
    {
        $authRequestValidator = AuthRequestValidator::createLoginValidator();
        Configure::write('Config.language', 'en');

        try {
            $this->assertTrue($authRequestValidator->validate([
                'email'    => "not email format",
                'password' => ""
            ]));
            // Expect to throw error
            $this->fail();
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            foreach ($e->getIterator() as $exception) {
                /** @var \Respect\Validation\Exceptions\ValidationException $exception */
                switch ($exception->getName()) {
                    case 'email':
                        $this->assertSame($exception->getMessage(), 'Email address is incorrect.');
                        break;
                    case 'password':
                        $this->assertSame($exception->getMessage(), '{{field}} is a required field.');
                        break;
                }
            }
        }
    }

    /**
     * Test for validation translate message
     *
     * @throws Exception
     */
    public function test_validatePost_success_ena()
    {
        $authRequestValidator = AuthRequestValidator::createLoginValidator();
        Configure::write('Config.language', 'ja');

        try {
            $this->assertTrue($authRequestValidator->validate([
                'email'    => "not email format",
                'password' => ""
            ]));
            // Expect to throw error
            $this->fail();
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            foreach ($e->getIterator() as $exception) {
                /** @var \Respect\Validation\Exceptions\ValidationException $exception */
                switch ($exception->getName()) {
                    case 'email':
                        $this->assertSame($exception->getMessage(), __('Email address is incorrect.'));
                        break;
                    case 'password':
                        $this->assertSame($exception->getMessage(), __('{{field}} is a required field.'));
                        break;
                }
            }
        }
    }
}
