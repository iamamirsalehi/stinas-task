<?php

namespace App\Services\Attachment;

use Symfony\Component\HttpFoundation\StreamedResponse;

interface AttachmentDownloadable
{
    public function download(string $filePath): StreamedResponse;
}