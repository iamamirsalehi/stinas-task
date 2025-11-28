<?php

namespace App\Http\Controllers\Admin\Ticket;

use App\Exception\BusinessException;
use App\Http\Controllers\Controller;
use App\Services\Ticket\TicketService;
use Illuminate\Http\Request;

class ShowTicketController extends Controller
{
    public function __construct(private TicketService $ticketService)
    {}
    
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $id)
    {
        try {
            $ticket = $this->ticketService->getByID($id);
            
            return view('admin.tickets.show', compact('ticket'));
        } catch (BusinessException $exception) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', $exception->getMessage());
        }
    }
}

