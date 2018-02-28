<?php

App::uses('VideoUploadRequest', 'Model/Video/Requests');
App::uses('VideoFileHasher', 'Lib/Video');

class VideoUploadRequestOnPost implements VideoUploadRequest
{
    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * hashed file string
     * @var string|null
     */
    protected $fileHash = null;

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
        if (empty($this->fileHash)) {
            $this->fileHash = VideoFileHasher::hashFile($this->file);
        }
        return $this->fileHash;
    }

    /**
     * @param string $envName
     *
     * @see https://confluence.goalous.com/display/GOAL/Video+storage+structure
     * @return string
     */
    public function getResourcePathFromEnvName(string $envName): string
    {
        return sprintf(
            'uploads/%s/%d/%d/%s/original',
            $envName,
            $this->teamId,
            $this->getUserId(),
            $this->getFileHash()
        );
    }

    /**
     * @see https://confluence.goalous.com/display/GOAL/Video+storage+structure
     * @return string
     */
    public function getResourcePath(): string
    {
        return $this->getResourcePathFromEnvName($this->estimateSeparatorString());
    }

    /**
     * return separator string to store in storage
     * e.g.
     *      'streams/<separator>/<team_id>/<user_id>/abcdef1234567890/'
     *      isao env: 'streams/isao/<team_id>/<user_id>/abcdef1234567890/'
     *      dev env: 'streams/dev/<team_id>/<user_id>/abcdef1234567890/'
     *      on someones local: 'streams/user_who_develop/<team_id>/<user_id>/abcdef1234567890/'
     *
     * @return string
     */
    private function estimateSeparatorString(): string
    {
        if (empty(ENV_NAME)) {
            throw new RuntimeException('ENV_NAME is must defined');
        }
        // On local, user defined separator name for directory separation
        if ('local' === ENV_NAME) {
            if (empty(AWS_S3_BUCKET_VIDEO_TRANSCODE_LOCAL_SEPARATOR)) {
                throw new RuntimeException('VIDEO_TRANSCODE_LOCAL_SEPARATOR is must defined on local');
            }
            return AWS_S3_BUCKET_VIDEO_TRANSCODE_LOCAL_SEPARATOR;
        }
        return ENV_NAME;
    }

    public function getContentType(): string
    {
        // the original video does not seen from user, content-type set to simple binary file
        return 'binary/octet-stream';
    }

    public function getBucket(): string
    {
        return AWS_S3_BUCKET_VIDEO_ORIGINAL;
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

    /**
     * set file hash
     * @param null|string $fileHash
     */
    public function setFileHash(string $fileHash)
    {
        $this->fileHash = $fileHash;
    }
}