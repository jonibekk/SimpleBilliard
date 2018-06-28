<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ErrorResponse', 'Lib/Network/Response');
App::uses('AuthRequestValidator', 'Validator/Request/Api/V2');

use Goalous\Enum as Enum;

/**
 * Class ErrorResponseTest
 */
class ErrorResponseTest extends GoalousTestCase
{
    function test_simpleError()
    {
        $response = (new ErrorResponse(ErrorResponse::RESPONSE_BAD_REQUEST))
            ->getResponse();

        $this->assertSame(
            json_encode([
                'message' => '',
                'errors' => []
            ]),
            $response->body()
        );

        $this->assertSame(
            ErrorResponse::RESPONSE_BAD_REQUEST,
            $response->statusCode()
        );
    }

    function test_onlyMessage()
    {
        $response = (new ErrorResponse(ErrorResponse::RESPONSE_BAD_REQUEST))
            ->withMessage(__METHOD__)
            ->getResponse();

        $this->assertSame(
            json_encode([
                'message' => __METHOD__,
                'errors' => []
            ]),
            $response->body()
        );
    }


    function test_withError()
    {
        $response = (new ErrorResponse(ErrorResponse::RESPONSE_BAD_REQUEST))
            ->withError(new ErrorTypeGlobal(__METHOD__))
            ->getResponse();

        $this->assertSame(
            json_encode([
                'message' => __METHOD__,
                'errors' => [
                    [
                        'type' => Enum\Network\Response\ErrorType::GLOBAL,
                        'message' => __METHOD__
                    ]
                ]
            ]),
            $response->body()
        );
    }

    function test_withMultiError()
    {
        $response = (new ErrorResponse(ErrorResponse::RESPONSE_BAD_REQUEST))
            ->withError(new ErrorTypeGlobal($errorMessage1 = __METHOD__.__LINE__))
            ->withError(new ErrorTypeGlobal($errorMessage2 = __METHOD__.__LINE__))
            ->getResponse();

        $this->assertSame(
            json_encode([
                'message' => $errorMessage1,
                'errors' => [
                    [
                        'type' => Enum\Network\Response\ErrorType::GLOBAL,
                        'message' => $errorMessage1,
                    ],
                    [
                        'type' => Enum\Network\Response\ErrorType::GLOBAL,
                        'message' => $errorMessage2,
                    ],
                ]
            ]),
            $response->body()
        );
    }

    function test_withMultiErrorWithMessage()
    {
        $response = (new ErrorResponse(ErrorResponse::RESPONSE_BAD_REQUEST))
            ->withMessage(__METHOD__)
            ->withError(new ErrorTypeGlobal($errorMessage1 = __METHOD__.__LINE__))
            ->withError(new ErrorTypeGlobal($errorMessage2 = __METHOD__.__LINE__))
            ->getResponse();

        $this->assertSame(
            json_encode([
                'message' => __METHOD__,
                'errors' => [
                    [
                        'type' => Enum\Network\Response\ErrorType::GLOBAL,
                        'message' => $errorMessage1,
                    ],
                    [
                        'type' => Enum\Network\Response\ErrorType::GLOBAL,
                        'message' => $errorMessage2,
                    ],
                ]
            ]),
            $response->body()
        );
    }

    function test_addErrorsFromValidation()
    {
        $authRequestValidator = AuthRequestValidator::createLoginValidator();

        $errorResponseBody = new ErrorResponse(ErrorResponse::RESPONSE_BAD_REQUEST);
        $errorResponseBody = $errorResponseBody->withMessage(__METHOD__);

        try {
            $authRequestValidator->validate([
                'email' => 'not_email_string',
                //'password' => '',
            ]);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            $errorResponseBody->addErrorsFromValidationException($e);
        }

        $response = $errorResponseBody->getResponse();

        $this->assertSame(
            json_encode([
                'message' => __METHOD__,
                'errors' => [
                    [
                        'type' => Enum\Network\Response\ErrorType::VALIDATION,
                        'field' => 'email',
                        'message' => 'email must be valid email',
                    ],
                    [
                        'type' => Enum\Network\Response\ErrorType::VALIDATION,
                        'field' => 'password',
                        'message' => 'Key password must be present',
                    ],
                ],
            ]),
            $response->body()
        );
    }
}
