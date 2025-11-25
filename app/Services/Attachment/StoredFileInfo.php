<?php

namespace App\Services\Attachment;

readonly class StoredFileInfo
{
    public function __construct(
        public string $path,
    )
    {
    }
}