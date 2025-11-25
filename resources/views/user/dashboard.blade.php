@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-semibold">My Tickets</h1>
        <a href="{{ route('dashboard.tickets.create.show') }}" class="px-4 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white border border-black dark:border-[#eeeeec] dark:hover:border-white rounded-sm font-medium transition-colors">
            Create New Ticket
        </a>
    </div>

    <div class="bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#FDFDFC] dark:bg-[#0a0a0a] border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                    @forelse($tickets ?? [] as $ticket)
                        <tr class="hover:bg-[#FDFDFC] dark:hover:bg-[#0a0a0a]">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium">{{ $ticket->title }}</div>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                No tickets yet. <a href="{{ route('dashboard.tickets.create.show') }}" class="text-[#f53003] dark:text-[#FF4433] hover:underline">Create your first ticket</a>
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
</div>
@endsection

