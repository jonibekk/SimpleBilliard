<?php
App::uses('AbstractErrorType', 'Lib/Network/Response/ErrorResponseBody');

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
            'type' => 'validation',
            'field' => $this->field,
            'message' => $this->getMessage(),
        ];
    }
}
