<?php

namespace App\Services\Attachment;

interface AttachmentService 
{
    public function store(string $tempPath, string $originalExtension): StoredFileInfo;
}