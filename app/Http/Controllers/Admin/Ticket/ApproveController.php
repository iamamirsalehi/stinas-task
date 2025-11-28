<?php

namespace App\Http\Controllers\Admin\Ticket;

use App\Exception\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ticket\ApproveRequest;
use App\Services\Ticket\ApproveTicket;
use App\Services\Ticket\TicketApproveService;

class ApproveController extends Controller
{
    public function __construct(private TicketApproveService $ticketApproveService)
    {
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(ApproveRequest $request, $id)
    {
        $approveTicket = new ApproveTicket(
            $id,
            $request->get('note'),
            $request->user(),
        );

        try{
            $this->ticketApproveService->approve($approveTicket);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Approved');
        }catch(BusinessException $exception){
            return redirect()
                ->route('admin.dashboard')
                ->with('error', $exception->getMessage());
        }
    }
}
