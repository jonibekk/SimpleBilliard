<?php
App::uses('AbstractErrorType', 'Lib/Network/Response/ErrorResponseBody');

use Goalous\Enum as Enum;

class ErrorTypeGlobal extends AbstractErrorType
{

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function toArray():array
    {
        return [
            'type' => Enum\Network\Response\ErrorType::GLOBAL,
            'message' => $this->getMessage(),
        ];
    }
}
