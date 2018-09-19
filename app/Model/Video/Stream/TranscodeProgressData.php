<?php

use Goalous\Enum as Enum;

interface TranscodeProgressData
{
    public function getProgressState(): Enum\Model\Video\VideoTranscodeProgress;
    public function isError(): bool;
    public function getError(): string;
    public function getWarning(): string;
    public function getJobId(): string;
}
