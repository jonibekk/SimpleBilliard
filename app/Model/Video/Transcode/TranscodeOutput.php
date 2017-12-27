<?php

/**
 * transcoderの出力内容
 *
 * Class TranscodeOutput
 */
interface TranscodeOutput
{
    /**
     * @param string $baseUrl
     *
     * @return VideoSource[]
     */
    public function getVideoSources(string $baseUrl) :array;
}
