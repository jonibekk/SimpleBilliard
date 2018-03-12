<?php

use Goalous\Model\Enum as Enum;

interface VideoUploadResult
{
    public function isSucceed(): bool;
    public function getResourcePath(): string;
    public function getErrorCode(): string;
    public function getErrorMessage(): string;
}
