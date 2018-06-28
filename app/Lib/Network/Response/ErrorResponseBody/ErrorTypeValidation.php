<?php
App::uses('AbstractErrorType', 'Lib/Network/Response/ErrorResponseBody');

use Goalous\Enum as Enum;

class ErrorTypeValidation extends AbstractErrorType
{
    /**
     * @var string
     */
    private $field = '';

    public function __construct(string $field, string $message)
    {
        $this->message = $message;
        $this->field = $field;
    }

    public function toArray():array
    {
        return [
            'type' => Enum\Network\Response\ErrorType::VALIDATION,
            'field' => $this->field,
            'message' => $this->getMessage(),
        ];
    }
}
