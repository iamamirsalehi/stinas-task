<?php

namespace App\Services\Attachment;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

class LocalAttachmentService implements AttachmentService, AttachmentDownloadable
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

    public function download(string $filePath): StreamedResponse
    {
        if (! $this->storage->exists($filePath)) {
            throw new FileNotFoundException('file not found');
        }

        $customName = basename($filePath); 
        $headers = [];

        return $this->storage->download($filePath, $customName, $headers);
    }
}