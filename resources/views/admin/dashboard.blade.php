@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Ticket Management</h1>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">Manage and review all tickets</p>
    </div>

    <form method="POST" action="" id="bulkActionForm">
        @csrf
        <input type="hidden" name="ticket_ids" id="bulkTicketIds" value="">

        <div class="mb-4 flex gap-4 items-center">
            <div class="flex items-center gap-2">
                <input 
                    type="checkbox" 
                    id="selectAll" 
                    class="w-4 h-4 border-[#e3e3e0] dark:border-[#3E3E3A] rounded focus:ring-[#f53003] dark:focus:ring-[#FF4433]"
                >
                <label for="selectAll" class="text-sm font-medium">Select All</label>
            </div>
            <div class="flex gap-2">
                <button 
                    type="button" 
                    onclick="bulkAction('approve')"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-sm font-medium transition-colors text-sm"
                >
                    Approve Selected
                </button>
                <button 
                    type="button" 
                    onclick="bulkAction('reject')"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-sm font-medium transition-colors text-sm"
                >
                    Reject Selected
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#FDFDFC] dark:bg-[#0a0a0a] border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input 
                                    type="checkbox" 
                                    id="selectAllHeader"
                                    class="w-4 h-4 border-[#e3e3e0] dark:border-[#3E3E3A] rounded focus:ring-[#f53003] dark:focus:ring-[#FF4433]"
                                >
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                        @forelse($tickets ?? [] as $ticket)
                            <tr class="hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a]">
                                <td class="px-6 py-4">
                                    <input 
                                        type="checkbox" 
                                        name="ticket_ids[]" 
                                        value="{{ $ticket->id }}"
                                        class="ticket-checkbox w-4 h-4 border-[#e3e3e0] dark:border-[#3E3E3A] rounded focus:ring-[#f53003] dark:focus:ring-[#FF4433]"
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium">{{ $ticket->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ $ticket->user->name ?? $ticket->user->username ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $status = $ticket->status instanceof \App\Enums\TicketStatus 
                                            ? $ticket->status->value 
                                            : ($ticket->status ?? 'submitted');
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 dark:bg-gray-900/30 text-gray-800 dark:text-gray-300',
                                            'submitted' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                                            'approved_by_admin1' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                            'approved_by_admin2' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                            'rejected_by_admin1' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                                            'rejected_by_admin2' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                                            'sent_to_webservice' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300'
                                        ];
                                        $color = $statusColors[$status] ?? $statusColors['submitted'];
                                        $displayStatus = str_replace('_', ' ', $status);
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                        {{ ucwords($displayStatus) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ $ticket->created_at ? $ticket->created_at->format('M d, Y') : now()->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a 
                                        href="{{ route('admin.tickets.show', $ticket->id) }}" 
                                        class="text-[#f53003] dark:text-[#FF4433] hover:underline"
                                    >
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    No tickets found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(isset($tickets) && $tickets->hasPages())
                <div class="px-6 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($tickets->onFirstPage())
                                <span class="px-3 py-1 text-sm text-[#706f6c] dark:text-[#A1A09A] cursor-not-allowed">Previous</span>
                            @else
                                <a href="{{ $tickets->previousPageUrl() }}" class="px-3 py-1 text-sm text-[#1b1b18] dark:text-[#eeeeec] hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm transition-colors">Previous</a>
                            @endif
                            
                            @php
                                $currentPage = $tickets->currentPage();
                                $lastPage = $tickets->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                            @endphp
                            
                            @if($startPage > 1)
                                <a href="{{ $tickets->url(1) }}" class="px-3 py-1 text-sm text-[#1b1b18] dark:text-[#eeeeec] hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm transition-colors">1</a>
                                @if($startPage > 2)
                                    <span class="px-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">...</span>
                                @endif
                            @endif
                            
                            @foreach($tickets->getUrlRange($startPage, $endPage) as $page => $url)
                                @if($page == $currentPage)
                                    <span class="px-3 py-1 text-sm bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] rounded-sm">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-1 text-sm text-[#1b1b18] dark:text-[#eeeeec] hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach
                            
                            @if($endPage < $lastPage)
                                @if($endPage < $lastPage - 1)
                                    <span class="px-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">...</span>
                                @endif
                                <a href="{{ $tickets->url($lastPage) }}" class="px-3 py-1 text-sm text-[#1b1b18] dark:text-[#eeeeec] hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm transition-colors">{{ $lastPage }}</a>
                            @endif
                            
                            @if($tickets->hasMorePages())
                                <a href="{{ $tickets->nextPageUrl() }}" class="px-3 py-1 text-sm text-[#1b1b18] dark:text-[#eeeeec] hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm transition-colors">Next</a>
                            @else
                                <span class="px-3 py-1 text-sm text-[#706f6c] dark:text-[#A1A09A] cursor-not-allowed">Next</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>

<script>
    // Select all functionality
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.ticket-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        document.getElementById('selectAllHeader').checked = this.checked;
    });

    document.getElementById('selectAllHeader')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.ticket-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        document.getElementById('selectAll').checked = this.checked;
    });

    // Bulk action
    function bulkAction(action) {
        const checkboxes = document.querySelectorAll('.ticket-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Please select at least one ticket.');
            return;
        }

        const ticketIds = Array.from(checkboxes).map(cb => cb.value);
        document.getElementById('bulkTicketIds').value = JSON.stringify(ticketIds);
        
        const form = document.getElementById('bulkActionForm');
        if (action === 'approve') {
            form.action = '{{ route("admin.tickets.bulk-approve") }}';
        } else {
            form.action = '{{ route("admin.tickets.bulk-reject") }}';
        }
        
        if (confirm(`Are you sure you want to ${action} ${ticketIds.length} ticket(s)?`)) {
            form.submit();
        }
    }
</script>
@endsection

