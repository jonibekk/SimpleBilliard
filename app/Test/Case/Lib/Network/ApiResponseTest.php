<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ApiResponse', 'Lib/Network/Response');

/**
 * Class ApiResponseTest
 */
class ApiResponseTest extends GoalousTestCase
{
    function test_ok()
    {
        $response = ApiResponse::ok()->getResponse();

        $this->assertSame(
            ApiResponse::RESPONSE_SUCCESS,
            $response->statusCode()
        );

        $this->assertSame(
            json_encode([]),
            $response->body()
        );
    }

    function test_simpleBody()
    {
        $bodyValue = [
            'value' => __LINE__,
        ];
        $response = (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))
            ->withBody($bodyValue)
            ->getResponse();

        $this->assertSame(
            ApiResponse::RESPONSE_SUCCESS,
            $response->statusCode()
        );

        $this->assertSame(
            json_encode($bodyValue),
            $response->body()
        );
    }

    function test_simpleData()
    {
        $dataValue = [
            'value' => __LINE__,
        ];
        $response = (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))
            ->withData($dataValue)
            ->getResponse();

        $this->assertSame(
            json_encode(['data' => $dataValue]),
            $response->body()
        );
    }

    function test_simpleDataWithMessage()
    {
        $dataValue = [
            'value' => __LINE__,
        ];
        $response = (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))
            ->withData($dataValue)
            ->withMessage(__METHOD__)
            ->getResponse();

        $this->assertSame(
            json_encode([
                'data' => $dataValue,
                'message' => __METHOD__,
            ]),
            $response->body()
        );
    }

    function test_header()
    {
        $response = (new ApiResponse(ApiResponse::RESPONSE_SUCCESS))
            ->withHeader([
                'X-Test' => ($headerValue = __METHOD__.__LINE__)
            ])
            ->getResponse();

        $this->assertSame('[]', $response->body());
        $this->assertSame($headerValue, $response->header()['X-Test']);
    }


}
