<?php

abstract class AbstractErrorType
{
    /**
     * @var string
     */
    protected $message = '';

    public function toArray(): array
    {
        throw new RuntimeException('need to implement function toArray()');
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}