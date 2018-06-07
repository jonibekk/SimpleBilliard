<?php

use Goalous\Enum as Enum;

interface VideoUploadRequest
{
    public function getObjectArray(): array;
    public function getResourcePath(): string ;
}
