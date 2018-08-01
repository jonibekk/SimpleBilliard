<?php
App::import('Lib/Upload', 'UploadedFile');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/31
 * Time: 10:47
 */
class UploadProcessor
{
    /** @var UploadedFile */
    protected $file;

    public final static function generate(UploadedFile $file): self
    {

        $type = $file->getFileType();

        switch ($type) {
            //Image rotation, resize
            case "image" :
                return new ImageProcessor();
                break;
            default:
                return new self();
                break;
        }
    }

    public function process(): UploadedFile
    {
        return $this->file;
    }

}