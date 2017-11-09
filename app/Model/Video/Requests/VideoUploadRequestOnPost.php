<?php

class VideoUploadRequestOnPost implements VideoUploadRequest
{
    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * array data of user
     * @var array
     */
    protected $user;

    /**
     * @var int
     */
    protected $teamId;

    /**
     * array data of draft post
     * @var array
     */
    protected $postDraft;

    public function __construct(\SplFileInfo $splFileInfo, array $user, int $teamId, array $postDraft)
    {
        $this->file = $splFileInfo;
        $this->user = $user;
        $this->teamId = $teamId;
        $this->postDraft = $postDraft;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    public function getSourceFilePath(): string
    {
        return $this->file->getRealPath();
    }

    private function getDraftPostId(): int
    {
        return intval($this->postDraft['id']);
    }

    private function getUserId(): int
    {
        return intval($this->user['id']);
    }

    private function getFileHash(): string
    {
        return hash_file('sha256', $this->getSourceFilePath());
    }

    public function getKey(): string
    {
        return sprintf(
            'uploads/%d/%d/%s/original',
            $this->getUserId(),
            $this->teamId,
            $this->getFileHash()
            );
    }

    public function getContentType(): string
    {
        // TODO: research this is no problem
        // the original video does not see from user
        return 'binary/octet-stream';
    }

    public function getBucket(): string
    {
        // TODO: move to config
        return 'goalous-local-masuichig-videos';
    }

    public function getAcl(): string
    {
        return 'private';
    }

    public function getMetaData(): array
    {
        return [
            // TODO: can s3 set original metadata by this ?
            'draft_posts.id' => $this->getDraftPostId(),
        ];
    }

    /**
     * returns array for compatible to AWS S3 bucket client options
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#putobject
     * @return array
     */
    public function getObjectArray(): array
    {
        return [
            'Bucket'       => $this->getBucket(),
            'Key'          => $this->getKey(),
            'SourceFile'   => $this->getSourceFilePath(),
            'ContentType'  => $this->getContentType(),
            'ACL'          => $this->getAcl(),
            'Metadata'     => $this->getMetaData(),
        ];
    }
}