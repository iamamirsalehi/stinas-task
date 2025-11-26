@extends('layouts.app')

@section('title', 'Ticket Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
            ← Back to Dashboard
        </a>
    </div>

    <div class="bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-8 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-semibold mb-2">{{ $ticket->title }}</h1>
                <div class="flex items-center gap-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    <span>Created by: <strong>{{ $ticket->user->name ?? $ticket->user->username ?? 'N/A' }}</strong></span>
                    <span>•</span>
                    <span>{{ $ticket->created_at ? $ticket->created_at->format('M d, Y H:i') : now()->format('M d, Y H:i') }}</span>
                </div>
            </div>
            <div>
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
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $color }}">
                    {{ ucwords($displayStatus) }}
                </span>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-medium mb-2">Description</h2>
            <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] whitespace-pre-wrap bg-[#FDFDFC] dark:bg-[#0a0a0a] p-4 rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A]">
                {{ $ticket->description }}
            </div>
        </div>

        @if($ticket->file_path)
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-2">Attachment</h2>
                <a 
                    href="{{ route('admin.tickets.download', $ticket->id) }}" 
                    target="_blank"
                    class="inline-flex items-center gap-2 text-sm text-[#f53003] dark:text-[#FF4433] hover:underline"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Download File ({{ basename($ticket->file_path) }})
                </a>
            </div>
        @endif

        @if($ticket->admin_note ?? false)
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-sm">
                <h2 class="text-lg font-medium mb-2">Admin Note</h2>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $ticket->admin_note }}</p>
            </div>
        @endif
    </div>

    @php
        $statusValue = $ticket->status instanceof \App\Enums\TicketStatus 
            ? $ticket->status->value 
            : ($ticket->status ?? 'submitted');
    @endphp
    
    @if($statusValue === 'submitted')
        <div class="bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-8">
            <h2 class="text-xl font-semibold mb-4">Take Action</h2>

            <form method="POST" action="{{ route('admin.tickets.approve', $ticket->id) }}" class="mb-6">
                @csrf
                <div class="mb-4">
                    <label for="approve_note" class="block text-sm font-medium mb-2">Note (Optional)</label>
                    <textarea 
                        id="approve_note" 
                        name="note" 
                        rows="3" 
                        placeholder="Add a note for approval..."
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] rounded-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"
                    >{{ old('note') }}</textarea>
                </div>
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-sm font-medium transition-colors"
                >
                    Approve Ticket
                </button>
            </form>

            <form method="POST" action="{{ route('admin.tickets.reject', $ticket->id) }}">
                @csrf
                <div class="mb-4">
                    <label for="reject_note" class="block text-sm font-medium mb-2">Note <span class="text-[#f53003] dark:text-[#FF4433]">*</span></label>
                    <textarea 
                        id="reject_note" 
                        name="note" 
                        rows="3" 
                        required
                        placeholder="Please provide a reason for rejection..."
                        class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] rounded-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                    >{{ old('note') }}</textarea>
                    @error('note')
                        <p class="mt-1 text-sm text-[#f53003] dark:text-[#FF4433]">{{ $message }}</p>
                    @enderror
                </div>
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-sm font-medium transition-colors"
                >
                    Reject Ticket
                </button>
            </form>
        </div>
    @endif
</div>
@endsection

