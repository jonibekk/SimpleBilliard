<?php
App::import('Lib/Storage/Processor/Image', 'BaseImageProcessor');
App::import('Lib/Storage', 'UploadedFile');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/21
 * Time: 11:28
 */
class ImageRotateProcessor extends BaseImageProcessor
{
    public function process(UploadedFile $file): UploadedFile
    {
        list($rotation, $isFlipped) = $this->getRotation($file);
        if ($rotation === 0 && $isFlipped === false) {
            return $file;
        }

        $image = imagecreatefromstring($file->getBinaryFile());

        if (!$image) {
            GoalousLog::error("Failed creating image resource");
            throw new RuntimeException("Failed creating image resource");
        }

        // Rotation
        $image = imagerotate($image, $rotation, 0);

        // Flipping
        if ($isFlipped && !imageflip($image, IMG_FLIP_HORIZONTAL)) {
            GoalousLog::error("Failed flipping image resource");
            throw new RuntimeException("Failed flipping image resource");
        }

        $imageString = $this->resourceToString($image, $file->getFileExt());

        // Destroy
        imagedestroy($image);

        return new UploadedFile($imageString, $file->getFileName(), true);
    }

    /**
     * Check rotation of an image
     *
     * @param UploadedFile $file
     *
     * @return array
     *              [degree, flipped]
     *              e.g. [90, true]
     */
    public function getRotation(UploadedFile $file): array
    {
        //Only jpeg & tiff support EXIF image data
        $imageType = $file->getFileExt();
        if ($imageType != 'jpeg' && $imageType != 'tiff') {
            return [0, false];
        }
        $exif = $this->readExif($file);
        $orientation = !empty($exif['Orientation']) ? $exif['Orientation'] : 1;
        switch ($orientation) {
            case 1: //通常
                return [0, false];
            case 2: //左右反転
                return [0, true];
            case 3: //180°回転
                return [180, false];
            case 4: //上下反転
                return [180, true];
            case 5: //反時計回りに90°回転 上下反転
                return [270, true];
            case 6: //反時計回りに90°回転
                return [270, false];
            case 7: //　時計回りに90°回転 上下反転
                return [90, true];
            case 8: //時計回りに90°回転
                return [90, false];
            default:
                throw new UnexpectedValueException("Unknown orientation value");
        }
    }

    private function readExif(UploadedFile $file): array
    {
        $exif = @exif_read_data("data://" . $file->getMIME() . ";base64," . $file->getEncodedFile());
        return $exif;
    }
}