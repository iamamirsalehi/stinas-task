<?php

namespace App\Http\Controllers\User\Ticket;

use App\Http\Controllers\Controller;
use App\Services\Ticket\TicketService;
use Illuminate\Http\Request;

class ListTicketController extends Controller
{
    public function __construct(private TicketService $ticketService)
    {}
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $tickets = $this->ticketService->list(
            (int) $request->input('per_page', 10),
            (int) $request->input('page', 1)
        );

        return view('user.dashboard', compact('tickets'));
    }
}
