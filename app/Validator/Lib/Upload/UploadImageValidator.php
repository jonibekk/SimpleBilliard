<?php

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/26
 * Time: 9:20
 */

class UploadImageValidator
{
    const MAX_PIXELS = 25 * 1000 * 1000;

    public static function validateResolution(UploadedFile $uploadedFile): bool
    {
        list($xLength, $yLength) = getimagesizefromstring($uploadedFile->getBinaryFile());

        if (empty ($xLength) || empty ($yLength)) {
            throw new RuntimeException("Resolution can't be empty");
        }

        return $xLength * $yLength <= self::MAX_PIXELS;
    }
}