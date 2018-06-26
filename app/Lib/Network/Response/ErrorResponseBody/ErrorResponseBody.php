<?php

App::uses('ErrorTypeGlobal', 'Lib/Network/Response/ErrorResponseBody');
App::uses('ErrorTypeValidation', 'Lib/Network/Response/ErrorResponseBody');

/**
 * This class make error response formatted array
 * https://confluence.goalous.com/x/gQIQAQ
 *
 * @see ErrorResponseBodyTest.php for the usage
 *
 * Class ErrorResponseBody
 */
class ErrorResponseBody
{
    /**
     * @var AbstractErrorType[]
     */
    private $errors = [];

    /**
     * @var string
     */
    private $message = '';

    /**
     * @param null $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param AbstractErrorType $errorType
     */
    public function addError(AbstractErrorType $errorType)
    {
        array_push($this->errors, $errorType);
    }

    /**
     * Returning error format response body by array
     * @return array
     */
    public function getBody(): array
    {
        return [
            'message' => $this->getMainMessage(),
            'errors' => array_map(function(AbstractErrorType $error) {
                return $error->toArray();
            }, $this->errors),
        ];
    }

    /**
     * Solve main message of error response
     * @return string
     */
    private function getMainMessage(): string
    {
        if (empty($this->errors)) {
            return $this->message;
        }

        if (empty($this->message)) {
            return reset($this->errors)->getMessage();
        }

        return $this->message;
    }

    public function addErrorsFromValidationException(\Respect\Validation\Exceptions\AllOfException $exception)
    {
        $validationExceptions = $exception->getIterator();

        foreach ($validationExceptions as $exception) {
            $this->addError(new ErrorTypeValidation($exception->getName(), $exception->getMessage()));
        }
    }
}
