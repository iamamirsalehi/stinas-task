<?php

namespace App\Http\Controllers\Admin\Ticket;

use App\Exception\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ticket\RejectRequest;
use App\Services\Ticket\RejectTicket;
use App\Services\Ticket\TicketRejectService;

class RejectController extends Controller
{
    public function __construct(private TicketRejectService $ticketRejectService)
    {
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(RejectRequest $request, $id)
    {
        $rejectTicket = new RejectTicket(
            $id,
            $request->user(),
            $request->get('note'),
        );

        try{
            $this->ticketRejectService->reject($rejectTicket);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Ticket rejected');
        }catch(BusinessException $exception){
            return redirect()
                ->route('admin.dashboard')
                ->with('error', $exception->getMessage());
        }
    }
}

