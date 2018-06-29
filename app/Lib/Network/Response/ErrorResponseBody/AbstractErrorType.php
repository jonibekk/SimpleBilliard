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
    abstract public function toArray(): array;

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}