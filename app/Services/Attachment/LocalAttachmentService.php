<?php

namespace App\Services\Attachment;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Str;

class LocalAttachmentService implements AttachmentService
{
    public function __construct(private Filesystem $storage)
    {
    }

    public function store(string $tempPath, string $originalExtension): StoredFileInfo
    {
        $uniqueFilename = Str::uuid()->toString();
        $uniqueFilename .= '.' . $originalExtension;
        
        $fileContents = file_get_contents($tempPath);
        $storedPath = 'attachments/tickets/' . $uniqueFilename;
        $this->storage->put($storedPath, $fileContents);
        
        return new StoredFileInfo($storedPath);
    }
}