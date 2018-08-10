<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/26
 * Time: 13:29
 */

/**
 * TODO GL-7224
 * Class UploadPreProcess
 */
class ImageProcessor extends UploadProcessor
{
    public function process(): UploadedFile
    {
        $image = $this->resizeImage($this->file);
        $image = $this->rotateImage($image);

        return $image;
    }

    private function rotateImage(UploadedFile $file): UploadedFile
    {
        //TODO GL-7224
        return $file;
    }

    private function resizeImage(UploadedFile $file): UploadedFile
    {
        //TODO GL-7224
        return $file;
    }
}