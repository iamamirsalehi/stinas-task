<?php

namespace App\Services\Ticket;

use App\Models\User;
use Illuminate\Http\UploadedFile;

readonly class AddNewTicket
{
    public function __construct(
        public string $title,
        public string $description,
        public UploadedFile $uploadedFile,
        public User $user,
    )
    {
    }
}