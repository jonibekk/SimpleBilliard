<?php
App::import('Lib/Storage/Processor', 'BaseUploadProcessor');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/21
 * Time: 11:49
 */
abstract class BaseImageProcessor extends BaseUploadProcessor
{
    /**
     * Convert imageGD resource back to binary string of image
     * There is no function to do this in the library, so image resource is output to
     * buffer as binary string, then capture it, and then clean the buffer.
     *
     * @param        $image ImageGD resource
     * @param string $type  Image type (jpeg, png, etc.)
     * @param int    $quality
     *
     * @return string
     */
    protected function resourceToString($image, string $type = "", int $quality = 75): string
    {
        if ($quality < 0 || $quality > 100) {
            throw new InvalidArgumentException("Invalid quality value");
        }

        //Convert GD resource to binary string
        ob_start();
        switch (strtolower($type)) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($image, null, $quality);
                break;
            case 'png':
                //Other image types use quality, but PNG use compression
                //PNG compression ranges from 0 (no compression) to 9 (max compression)
                $compression = (100 - $quality) / 10;
                if ($compression > 9) {
                    $compression = 9;
                }
                imagepng($image, null, $compression);
                break;
            case 'gif':
                imagegif($image, null, $quality);
                break;
            case 'bmp':
                imagebmp($image, null, $quality);
                break;
            default:
                throw new InvalidArgumentException("Unknown image type");
        }
        unset($image);
        $imageString = ob_get_contents();
        ob_end_clean();

        return $imageString;
    }
}