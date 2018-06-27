<?php

/**
 * Base class of response error type
 *
 * Class AbstractErrorType
 */
abstract class AbstractErrorType
{
    /**
     * @var string
     */
    protected $message = '';

    /**
     * Building a unit of error reason
     * @see https://confluence.goalous.com/x/gQIQAQ#APIv2ResponseFormat-Errorreasonunittype
     *
     * @return array
     */
    public function toArray(): array
    {
        throw new RuntimeException('need to implement function toArray()');
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}