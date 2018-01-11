<?php

class VideoFileHasher
{
    public static function hashFile(\SplFileInfo $splFileInfo): string
    {
        return hash_file('sha256', $splFileInfo->getRealPath());
    }
}