<?php

App::uses('AwsVideoTranscodeRequest', 'Model/Video/Requests');

use Goalous\Model\Enum as Enum;

class AwsVideoTranscodeJobResult
{
    protected $data;

    private $errorCode;
    private $errorMessage;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getJobId()
    {
        return Hash::get($this->data, 'Job.Id', null);
    }

    public function isSucceed(): bool
    {
        return Hash::check($this->data, 'Job.Id');
    }

    public function withErrorCodeAws($code): self
    {
        $this->errorCode = $code;
        return $this;
    }

    public function withErrorMessage(string $message): self
    {
        $this->errorMessage = $message;
        return $this;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode ?? '';
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage ?? '';
    }
}
