<?php

/**
 * this is define of the
 * hash string from video file
 *
 * Class VideoFileHasher
 */
class VideoFileHasher
{
    public static function hashFile(\SplFileInfo $splFileInfo): string
    {
        return hash_file('sha256', $splFileInfo->getRealPath());
    }
}