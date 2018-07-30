<?php
App::import('Lib/Upload', 'UploadedFile');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/24
 * Time: 14:25
 */
class UploadRedisData
{
    /**
     * Uploaded file
     *
     * @var UploadedFile
     */
    private $rawFile;

    /**
     * @var int|null
     */
    private $timeToLive;

    public function __construct(UploadedFile $file)
    {
        $this->rawFile = $file;
    }

    public function withFile(UploadedFile $file): self
    {
        $this->rawFile = $file;
        return $this;
    }

    public function getFile(): UploadedFile
    {
        return $this->rawFile;
    }

    /**
     * @return int|null
     */
    public function getTimeToLive()
    {
        return $this->timeToLive;
    }

    /**
     * @param int $timeToLive
     *
     * @return $this
     */
    public function withTimeToLive(int $timeToLive): self
    {
        $this->timeToLive = $timeToLive;
        return $this;
    }

}