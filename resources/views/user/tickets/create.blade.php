@extends('layouts.app')

@section('title', 'Create Ticket')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('user.dashboard') }}" class="text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
            ← Back to Dashboard
        </a>
    </div>

    <div class="bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-8">
        <h1 class="text-2xl font-semibold mb-6">Create New Ticket</h1>

        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium mb-2">Title <span class="text-[#f53003] dark:text-[#FF4433]">*</span></label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title') }}" 
                    required 
                    autofocus
                    placeholder="Enter ticket title"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] rounded-sm focus:outline-none focus:ring-2 focus:ring-[#f53003] dark:focus:ring-[#FF4433]"
                >
                @error('title')
                    <p class="mt-1 text-sm text-[#f53003] dark:text-[#FF4433]">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium mb-2">Description <span class="text-[#f53003] dark:text-[#FF4433]">*</span></label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="6" 
                    required
                    placeholder="Enter ticket description"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] rounded-sm focus:outline-none focus:ring-2 focus:ring-[#f53003] dark:focus:ring-[#FF4433] resize-none"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-[#f53003] dark:text-[#FF4433]">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="file" class="block text-sm font-medium mb-2">Attachment</label>
                <input 
                    type="file" 
                    id="file" 
                    name="file" 
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] rounded-sm focus:outline-none focus:ring-2 focus:ring-[#f53003] dark:focus:ring-[#FF4433] file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-sm file:font-medium file:bg-[#FDFDFC] dark:file:bg-[#0a0a0a] file:text-[#1b1b18] dark:file:text-[#EDEDEC] hover:file:bg-[#e3e3e0] dark:hover:file:bg-[#3E3E3A]"
                >
                <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG</p>
                @error('file')
                    <p class="mt-1 text-sm text-[#f53003] dark:text-[#FF4433]">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white border border-black dark:border-[#eeeeec] dark:hover:border-white rounded-sm font-medium transition-colors"
                >
                    Submit Ticket
                </button>
                <a 
                    href="{{ route('user.dashboard') }}" 
                    class="px-6 py-2 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] rounded-sm font-medium transition-colors"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

