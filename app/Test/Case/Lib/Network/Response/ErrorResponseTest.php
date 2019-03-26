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
    function test_eachStatus()
    {
        $this->assertSame(400, ErrorResponse::badRequest()->getResponse()->statusCode());
        $this->assertSame(401, ErrorResponse::unauthorized()->getResponse()->statusCode());
        $this->assertSame(403, ErrorResponse::forbidden()->getResponse()->statusCode());
        $this->assertSame(404, ErrorResponse::notFound()->getResponse()->statusCode());
        $this->assertSame(409, ErrorResponse::resourceConflict()->getResponse()->statusCode());
        $this->assertSame(500, ErrorResponse::internalServerError()->getResponse()->statusCode());
    }

    function test_simpleError()
    {
        $response = (new ErrorResponse(ErrorResponse::RESPONSE_INTERNAL_SERVER_ERROR))
            ->getResponse();

        $this->assertSame(
            json_encode([
                'message' => __('Server error occurred. We apologize for the inconvenience. Please try again.'),
                'errors' => []
            ]),
            $response->body()
        );

        $this->assertSame(
            ErrorResponse::RESPONSE_INTERNAL_SERVER_ERROR,
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
                'message' => __('Validation failed.'),
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
                'message' => __('Validation failed.'),
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

        $expectedBody = [
            'message' => 'Email address is incorrect.',
            'errors' => [
                [
                    'type' => Enum\Network\Response\ErrorType::VALIDATION,
                    'field' => 'email',
                    'message' => 'Email address is incorrect.',
                ],
                [
                    'type' => Enum\Network\Response\ErrorType::VALIDATION,
                    'field' => 'password',
                    'message' => 'Key password must be present',
                ],
            ],
        ];
        $actualBody = json_decode($response->body(), true);

        $this->assertEquals($expectedBody['message'], $actualBody['message']);
        $this->assertEquals(count($expectedBody['errors']), count($actualBody['errors']));
        for ($i = 0; $i < count($expectedBody['errors']); $i++) {
            $this->assertEquals($expectedBody['errors'][$i]['type'], $actualBody['errors'][$i]['type']);
            $this->assertEquals($expectedBody['errors'][$i]['field'], $actualBody['errors'][$i]['field']);
        }
    }
}
