<?php

namespace App\Http\Controllers\Admin\Ticket;

use App\Exception\BusinessException;
use App\Http\Controllers\Controller;
use App\Services\Role\RoleAccessService;
use App\Services\Ticket\TicketService;
use Illuminate\Http\Request;

class ListTicketController extends Controller
{
    public function __construct(
        private TicketService $ticketService,
        private RoleAccessService $roleAccessService,    
    )
    {}
    
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);

        try {
            $statuses = $this->roleAccessService->getTasksStatuses($request->user());

            $tickets = $this->ticketService->list($perPage, $page, $statuses);
        }catch(BusinessException $exception){
            return redirect()
                ->route('admin.dashboard')
                ->with('error', $exception->getMessage());
        }

        if ($request->has('per_page')) {
            $tickets->appends(['per_page' => $perPage]);
        }

        return view('admin.dashboard', compact('tickets'));
    }
}

