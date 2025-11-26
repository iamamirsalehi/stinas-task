<?php

namespace App\Http\Controllers\Admin\Ticket;

use App\Exception\BusinessException;
use App\Http\Controllers\Controller;
use App\Services\Attachment\AttachmentDownloadable;
use App\Services\Ticket\TicketService;
use Illuminate\Http\Request;

class DownloadTicketFileController extends Controller
{
    public function __construct(
        private TicketService $ticketService,
        private AttachmentDownloadable $attachmentDownloadable,    
    )
    {}
    
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id)
    {
        try {
            $ticket = $this->ticketService->getByID($id);
            
            if(!$ticket->hasFile()) {
                return redirect()
                ->route('admin.dashboard')
                ->with('error', 'ticket does not have file');
            }
            
            return $this->attachmentDownloadable->download($ticket->file_path);
        } catch (BusinessException $e) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', $e->getMessage());
        }
    }
}

