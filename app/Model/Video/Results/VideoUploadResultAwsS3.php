<?php

class VideoUploadResultAwsS3 implements VideoUploadResult
{
    /**
     * @var array
     */
    protected $data;

    private $errorCode;
    private $errorMessage;


    private function __construct(array $data)
    {
        $this->data = $data;
    }

    public function isSucceed(): bool
    {
        if (isset($this->data['@metadata']['statusCode'])) {
            return 200 === $this->data['@metadata']['statusCode'];
        }
        return false;
    }

    public static function createFromGuzzleModel(\Guzzle\Service\Resource\Model $model): self
    {
        return new self($model->toArray());
    }

    public static function createFromAwsException(\Aws\Common\Exception\ServiceResponseException $exception): self
    {
        return (new self([]))
            ->withErrorCodeAws($exception->getAwsErrorCode())
            ->withErrorMessage($exception->getMessage());
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

    public function getErrorCodeAws(): int
    {
        return intval($this->errorCode);
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
