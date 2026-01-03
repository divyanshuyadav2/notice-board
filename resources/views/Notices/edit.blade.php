@extends('layouts.layout')

@section('title', 'Under Development')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="bg-[#0b2436] border border-[#1f425a] rounded-xl p-8 max-w-md w-full text-center shadow-lg">

        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 flex items-center justify-center rounded-full bg-yellow-500/10 text-yellow-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v3m0 4h.01M4.93 4.93l14.14 14.14M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                </svg>
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-xl font-semibold text-white mb-2">
            Under Development
        </h2>

        <!-- Description -->
        <p class="text-gray-400 text-sm leading-relaxed mb-6">
            This section is currently under development.
            We’re working hard to bring this feature live soon.
        </p>

        <!-- Actions -->
        <div class="flex justify-center gap-3">
            <a href="{{ route('notices.index') }}"
               class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                Go Back
            </a>

            <button disabled
                class="px-4 py-2 rounded bg-gray-600 text-gray-300 text-sm cursor-not-allowed">
                Coming Soon
            </button>
        </div>
    </div>
</div>
@endsection
