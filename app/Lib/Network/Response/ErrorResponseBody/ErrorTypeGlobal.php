<?php
App::uses('AbstractErrorType', 'Lib/Network/Response/ErrorResponseBody');

class ErrorTypeGlobal extends AbstractErrorType
{

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function toArray():array
    {
        return [
            'type' => 'global',
            'message' => $this->getMessage(),
        ];
    }
}
