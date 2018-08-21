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
    protected function resourceToString($image, string $type = "", int $quality = 75): string
    {
        if ($quality < 0 || $quality > 100){
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
                $quality = $quality / 10;
                if ($quality > 9) {
                    $quality = 9;
                }
                imagepng($image, null, $quality);
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