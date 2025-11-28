<?php

namespace App\Http\Controllers\Admin\Ticket;

use App\Exception\BusinessException;
use App\Http\Controllers\Controller;
use App\Services\Ticket\TicketApproveService;
use Illuminate\Http\Request;

class BulkApproveController extends Controller
{
    public function __construct(
        private TicketApproveService $ticketApproveService,
    )
    {
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|string',
        ]);

        $ticketIDs = json_decode($request->input('ticket_ids'), true);

        if (!is_array($ticketIDs) || empty($ticketIDs)) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Please select at least one ticket.');
        }

        $admin = $request->user();

        try {
            $this->ticketApproveService->approveBulk($ticketIDs, $admin);

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Tickets approved successfully.');
        } catch (BusinessException $exception) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', $exception->getMessage());
        }
    }
}

