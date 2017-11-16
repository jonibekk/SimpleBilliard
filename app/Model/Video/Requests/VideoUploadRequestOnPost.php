<?php

App::uses('VideoUploadRequest', 'Model/Video/Requests');

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

    protected $video;
    protected $videoStream;

    public function __construct(\SplFileInfo $splFileInfo, array $user, int $teamId, array $video, array $videoStream)
    {
        $this->file = $splFileInfo;
        $this->user = $user;
        $this->teamId = $teamId;
        $this->video = $video;
        $this->videoStream = $videoStream;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    public function getSourceFilePath(): string
    {
        return $this->file->getRealPath();
    }

    private function getUserId(): int
    {
        return intval($this->user['id']);
    }

    public function getFileHash(): string
    {
        // TODO: any cache?
        return hash_file('sha256', $this->getSourceFilePath());
    }

    // TODO: rename to kind of resource path
    public function getResourcePath(): string
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
        // TODO: define fqdn to extra_define
        if (ENV_NAME == 'local') {
            return 'goalous-local-masuichig-videos-original';
        } else if (ENV_NAME == 'dev') {
            return 'goalous-dev-videos-original';
        }
    }

    public function getAcl(): string
    {
        return 'private';
    }

    public function getMetaData(): array
    {
        return [
            'videos.id' => $this->video['id'],
            'video_streams.id' => $this->videoStream['id'],
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
            'Key'          => $this->getResourcePath(),
            'SourceFile'   => $this->getSourceFilePath(),
            'ContentType'  => $this->getContentType(),
            'ACL'          => $this->getAcl(),
            'Metadata'     => $this->getMetaData(),
        ];
    }
}