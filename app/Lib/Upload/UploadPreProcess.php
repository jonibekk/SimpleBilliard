<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/26
 * Time: 13:29
 */

class UploadPreProcess
{
    /**
     * Process files depending on their type
     *
     * @param UploadedFile $file
     *
     * @return UploadedFile
     */
    public function process(UploadedFile $file): UploadedFile
    {
        $type = $file->getFileType();

        switch ($type) {
            //Image rotation, resize
            case "image" :
                $image = $this->resizeImage($file);
                $image = $this->rotateImage($image);
                return $image;
                break;
            default:
                break;
        }

        return $file;
    }

    private function rotateImage(UploadedFile $file): UploadedFile
    {

    }

    private function resizeImage(UploadedFile $file): UploadedFile
    {

    }
}