<?php

/**
 * transcoderの出力内容
 *
 * Class TranscodeOutput
 */
interface TranscodeOutput
{
    /**
     * @param string|null $baseUrl
     *
     * @return VideoSource[]
     */
    public function getVideoSources($baseUrl = null) :array;
}
