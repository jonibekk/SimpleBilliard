<?php
App::uses('VideoUploadResult', 'Model/Video/Results');

class VideoUploadResultAwsS3 implements VideoUploadResult
{
    /**
     * @var array
     */
    protected $data;
    protected $resourcePath;

    private $errorCode;
    private $errorMessage;


    private function __construct(array $data)
    {
        $this->data = $data;
    }

    public function isSucceed(): bool
    {
        // aws-sdk-php is version 2.x, cant use @metadata
        //if (isset($this->data['@metadata']['statusCode'])) {
        //    return 200 === $this->data['@metadata']['statusCode'];
        //}
        return 0 < strlen($this->data['ObjectURL']);
    }

    public static function createFromAwsResult(\Aws\Result $result): self
    {
        return new self($result->toArray());
    }

    public static function createFromAwsException(\Aws\Exception\AwsException $exception): self
    {
        return (new self([]))
            ->withErrorCodeAws($exception->getAwsErrorCode())
            ->withErrorMessage($exception->getMessage());
    }

    public function withResourcePath(string $resourcePath): self
    {
        $this->resourcePath = $resourcePath;
        return $this;
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

    public function getResourcePath(): string
    {
        return $this->resourcePath;
    }

    public function getErrorCode(): string
    {
        return strval($this->errorCode);
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
