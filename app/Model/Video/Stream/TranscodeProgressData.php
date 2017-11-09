<?php

use Goalous\Model\Enum as Enum;

interface TranscodeProgressData
{
    public function getProgress(): Enum\Video\VideoTranscodeProgress;
    public function isError(): bool;
    public function getError(): string;
}
