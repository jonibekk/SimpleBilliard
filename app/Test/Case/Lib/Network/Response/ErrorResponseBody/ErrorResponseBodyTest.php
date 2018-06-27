<?php

App::uses('GoalousTestCase', 'Test');
App::uses('ErrorResponseBody', 'Lib/Network/Response/ErrorResponseBody');
App::uses('AuthRequestValidator', 'Validator/Request/Api/V2');

/**
 * Class ErrorResponseBodyTest
 */
class ErrorResponseBodyTest extends GoalousTestCase
{
    function test_withMultipleError()
    {
        $errorResponseBody = new ErrorResponseBody();
        $errorResponseBody->setMessage(__METHOD__);
        $errorResponseBody->addError(new ErrorTypeGlobal($messageTypeGlobal = __METHOD__.__LINE__));
        $errorResponseBody->addError(new ErrorTypeValidation($fieldName = 'field_name', $messageTypeValidation = __METHOD__.__LINE__));

        $this->assertSame([
            'message' => __METHOD__,
            'errors' => [
                [
                    'type' => 'global',
                    'message' => $messageTypeGlobal,
                ],
                [
                    'type' => 'validation',
                    'field' => $fieldName,
                    'message' => $messageTypeValidation,
                ],
            ],
        ], $errorResponseBody->getBody());
    }

    function test_messageFromFirstError()
    {
        $errorResponseBody = new ErrorResponseBody();
        $errorResponseBody->addError(new ErrorTypeGlobal($messageTypeGlobal1 = __METHOD__.__LINE__));
        $errorResponseBody->addError(new ErrorTypeGlobal($messageTypeGlobal2 = __METHOD__.__LINE__));

        $this->assertSame([
            'message' => $messageTypeGlobal1,
            'errors' => [
                [
                    'type' => 'global',
                    'message' => $messageTypeGlobal1,
                ],
                [
                    'type' => 'global',
                    'message' => $messageTypeGlobal2,
                ],
            ],
        ], $errorResponseBody->getBody());
    }

    function test_onlyMessage()
    {
        $errorResponseBody = new ErrorResponseBody();
        $errorResponseBody->setMessage(__METHOD__);

        $this->assertSame([
            'message' => __METHOD__,
            'errors' => [],
        ], $errorResponseBody->getBody());
    }

    function test_addErrorsFromValidation()
    {
        $authRequestValidator = AuthRequestValidator::createLoginValidator();

        $errorResponseBody = new ErrorResponseBody();
        $errorResponseBody->setMessage(__METHOD__);
        try {
            $authRequestValidator->validate([
                'email' => 'not_email_string',
                //'password' => '',
            ]);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            $errorResponseBody->addErrorsFromValidationException($e);
        }

        $this->assertSame([
            'message' => __METHOD__,
            'errors' => [
                [
                    'type' => 'validation',
                    'field' => 'email',
                    'message' => 'email must be valid email',
                ],
                [
                    'type' => 'validation',
                    'field' => 'password',
                    'message' => 'Key password must be present',
                ],
            ],
        ], $errorResponseBody->getBody());
    }
}
