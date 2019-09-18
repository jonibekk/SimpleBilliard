<?php
App::import('Lib/Storage/Processor/Image', 'BaseImageProcessor');
App::import('Lib/Storage', 'UploadedFile');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/21
 * Time: 11:28
 */
class ImageResizeProcessor extends BaseImageProcessor
{
    /**
     * @param UploadedFile $file
     * @param string       $newGeometry Target image size
     *                                  [100x100] Band resize
     *                                  f[100x100] Forced resize
     *                                  100x100 Best resize
     *                                  100w Set width to 100
     *                                  100h Set height to 100
     *                                  100l Set max(height, width) to 100
     * @param int          $quality     Target quality. 0-100
     *
     * @return UploadedFile
     */
    public function process(
        UploadedFile $file,
        string $newGeometry = "",
        int $quality = 75
    ): UploadedFile {

        if (empty($newGeometry)) {
            return $file;
        }

        if ($quality < 0 || $quality > 100){
            throw new InvalidArgumentException("Invalid quality value");
        }

        //Create Image resource from binary string
        $sourceImage = @imagecreatefromstring($file->getBinaryFile());

        if (!$sourceImage) {
            GoalousLog::error("Failed creating image resource");
            throw new RuntimeException("Failed creating image resource");
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        //Determine destination dimensions and resize mode from provided geometry
        if (preg_match('/^\\[[\\d]+x[\\d]+\\]$/', $newGeometry)) {
            //Resize with banding
            list($targetWidth, $targetHeight) = explode('x', substr($newGeometry, 1, strlen($newGeometry) - 2));
            $resizeMode = 'band';
        } elseif (preg_match("/^f\[(\d+)x(\d+)\]$/", $newGeometry, $match)) {
            //Resize with force
            $targetWidth = $match[1];
            $targetHeight = $match[2];
            $resizeMode = 'force';
        } elseif (preg_match('/^[\\d]+x[\\d]+$/', $newGeometry)) {
            //Cropped resize (best fit)
            list($targetWidth, $targetHeight) = explode('x', $newGeometry);
            $resizeMode = 'best';
        } elseif (preg_match('/^[\\d]+w$/', $newGeometry)) {
            //Calculate height according to aspect ratio
            $targetWidth = (int)$newGeometry;
            $resizeMode = false;
        } elseif (preg_match('/^[\\d]+W$/', $newGeometry)) {
            //New W rule. If target width is larger than source, don't resize
            //Calculate height according to aspect ratio
            $targetWidth = (int)$newGeometry;
            if ($targetWidth > $sourceWidth) {
                $targetWidth = $sourceWidth;
            }
            $resizeMode = false;
        } elseif (preg_match('/^[\\d]+h$/', $newGeometry)) {
            //Calculate width according to aspect ratio
            $targetHeight = (int)$newGeometry;
            $resizeMode = false;
        } elseif (preg_match('/^[\\d]+H$/', $newGeometry)) {
            //New H rule. If target height is larger than source, don't resize
            //Calculate width according to aspect ratio
            $targetHeight = (int)$newGeometry;
            if ($targetHeight > $sourceHeight) {
                $targetHeight = $sourceHeight;
            }
            $resizeMode = false;
        } elseif (preg_match('/^[\\d]+l$/', $newGeometry)) {
            //Calculate shortest side according to aspect ratio
            if ($sourceWidth > $sourceHeight) {
                $targetWidth = (int)$newGeometry;
            } else {
                $targetHeight = (int)$newGeometry;
            }
            $resizeMode = false;
        } elseif (preg_match('/^[\\d]+L$/', $newGeometry)) {
            //New L rule. If longest dimension (width / height) is larger than resource, don't resize
            //Calculate shortest side according to aspect ratio
            if ($sourceWidth > $sourceHeight) {
                $targetWidth = (int)$newGeometry;
                if ($targetWidth > $sourceWidth) {
                    $targetWidth = $sourceWidth;
                }
            } else {
                $targetHeight = (int)$newGeometry;
                if ($targetHeight > $sourceHeight) {
                    $targetHeight = $sourceHeight;
                }
            }
            $resizeMode = false;
        } else {
            throw new InvalidArgumentException("Unknown geometry setting");
        }

        if (!isset($targetWidth)) {
            /** @noinspection PhpUndefinedVariableInspection */
            $targetWidth = ($targetHeight / $sourceHeight) * $sourceWidth;
        }
        if (!isset($targetHeight)) {
            $targetHeight = ($targetWidth / $sourceWidth) * $sourceHeight;
        }

        //Determine resize dimensions from appropriate resize mode and ratio
        /** @noinspection PhpUndefinedVariableInspection */
        if ($resizeMode === 'best') {
            // "best fit" mode
            if ($sourceWidth > $sourceHeight) {
                if ($sourceHeight / $targetHeight > $sourceWidth / $targetWidth) {
                    $ratio = $targetWidth / $sourceWidth;
                } else {
                    $ratio = $targetHeight / $sourceHeight;
                }
            } else {
                if ($sourceHeight / $targetHeight < $sourceWidth / $targetWidth) {
                    $ratio = $targetHeight / $sourceHeight;
                } else {
                    $ratio = $targetWidth / $sourceWidth;
                }
            }
            $resizeW = $sourceWidth * $ratio;
            $resizeH = $sourceHeight * $ratio;
        } elseif ($resizeMode === 'band') {
            // "banding" mode
            if ($sourceWidth > $sourceHeight) {
                $ratio = $targetWidth / $sourceWidth;
            } else {
                $ratio = $targetHeight / $sourceHeight;
            }
            $resizeW = $sourceWidth * $ratio;
            $resizeH = $sourceHeight * $ratio;
        } else {
            // no resize ratio
            $resizeW = $targetWidth;
            $resizeH = $targetHeight;
        }

        $img = imagecreatetruecolor($targetWidth, $targetHeight);

        switch ($file->getFileExt()) {
            case 'gif':
                $alphaColor = imagecolortransparent($sourceImage);
                imagefill($img, 0, 0, $alphaColor);
                imagecolortransparent($img, $alphaColor);
                break;
            case 'png':
                imagealphablending($img, false);
                imagesavealpha($img, true);
                break;
            default:
                imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));
                break;
        }

        imagecopyresampled($img, $sourceImage, ($targetWidth - $resizeW) / 2, ($targetHeight - $resizeH) / 2, 0, 0,
            $resizeW, $resizeH,
            $sourceWidth, $sourceHeight);

        $imageString = $this->resourceToString($img, $file->getFileExt(), $quality);

        return new UploadedFile($imageString, $file->getFileName(), true);
    }
}