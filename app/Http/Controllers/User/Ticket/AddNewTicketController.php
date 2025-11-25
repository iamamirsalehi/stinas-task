<?php

namespace App\Http\Controllers\User\Ticket;

use App\Exception\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Ticket\AddNewTicketRequest;
use App\Services\Ticket\AddNewTicket;
use App\Services\Ticket\TicketService;

class AddNewTicketController extends Controller
{
    public function __construct(private TicketService $ticketService)
    {}
    /**
     * Handle the incoming request.
     */
    public function __invoke(AddNewTicketRequest $request)
    {
        $addNewTicket = new AddNewTicket(
            $request->get('title'),
            $request->get('description'),
            $request->file('attachment'),
            $request->user(),
        );

        try {
            $this->ticketService->add($addNewTicket);

            return redirect()->route('dashboard.')
            ->with('success', 'You have been successfully logged in!');
        } catch(BusinessException $exception){
            return redirect()->back()
                ->with('error', $exception->getMessage());
        }
    }
}
