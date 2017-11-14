<?php

use Goalous\Model\Enum as Enum;

interface VideoUploadRequest
{
    public function getObjectArray(): array;
    public function getResourcePath(): string ;
}
