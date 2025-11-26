<?php

namespace App\Http\Controllers\Admin\Ticket;

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
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);

        $tickets = $this->ticketService->list($perPage, $page);

        if ($request->has('per_page')) {
            $tickets->appends(['per_page' => $perPage]);
        }

        return view('admin.dashboard', compact('tickets'));
    }
}

