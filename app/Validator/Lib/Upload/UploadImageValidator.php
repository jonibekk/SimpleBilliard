<?php

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/26
 * Time: 9:20
 */

class UploadImageValidator
{
    const MAX_WIDTH = 1000;
    const MAX_HEIGHT = 1000;

    public static function validateResolution(UploadedFile $uploadedFile): bool
    {
        $metadata = $uploadedFile->getMetadata();

        $metadataArray = explode(',', $metadata);

        //Resolution info is stored in 2nd last element
        $resolution = $metadataArray[count($metadataArray) - 2];

        list($xLength, $yLength) = explode('x', $resolution);

        if (empty ($xLength) || empty ($yLength)) {
            throw new RuntimeException("Resolution can't be empty");
        }

        return $xLength <= self::MAX_WIDTH && $yLength <= self::MAX_HEIGHT;
    }
}