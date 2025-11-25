@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Ticket Management</h1>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">Manage and review all tickets</p>
    </div>

    <form method="POST" action="{{ route('admin.tickets.bulk-action') }}" id="bulkActionForm">
        @csrf
        <input type="hidden" name="action" id="bulkAction" value="">
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
                                        value="{{ $ticket->id ?? 1 }}"
                                        class="ticket-checkbox w-4 h-4 border-[#e3e3e0] dark:border-[#3E3E3A] rounded focus:ring-[#f53003] dark:focus:ring-[#FF4433]"
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium">{{ $ticket->title ?? 'Sample Ticket Title' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ $ticket->user->name ?? 'John Doe' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $status = $ticket->status ?? 'pending';
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                                            'approved' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                            'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
                                        ];
                                        $color = $statusColors[$status] ?? $statusColors['pending'];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                    {{ $ticket->created_at ?? now()->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a 
                                        href="{{ route('admin.tickets.show', $ticket->id ?? 1) }}" 
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
        document.getElementById('bulkAction').value = action;
        document.getElementById('bulkTicketIds').value = JSON.stringify(ticketIds);
        
        if (confirm(`Are you sure you want to ${action} ${ticketIds.length} ticket(s)?`)) {
            document.getElementById('bulkActionForm').submit();
        }
    }
</script>
@endsection

