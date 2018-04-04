<?php

use Goalous\Model\Enum as Enum;

interface TranscodeProgressData
{
    public function getProgressState(): Enum\Video\VideoTranscodeProgress;
    public function isError(): bool;
    public function getError(): string;
    public function getWarning(): string;
    public function getJobId(): string;
}