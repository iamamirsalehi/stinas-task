<?php

namespace App\Http\Controllers\Admin\Ticket;

use App\Exception\TicketException;
use App\Http\Controllers\Controller;
use App\Services\Ticket\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadTicketFileController extends Controller
{
    public function __construct(private TicketService $ticketService)
    {}
    
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id)
    {
        try {
            $ticket = $this->ticketService->show($id);
            
            if (!$ticket->file_path || !Storage::disk('local')->exists($ticket->file_path)) {
                return redirect()
                    ->route('admin.tickets.show', $id)
                    ->with('error', 'File not found');
            }
            
            return Storage::disk('local')->download($ticket->file_path);
        } catch (TicketException $e) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', $e->getMessage());
        }
    }
}

