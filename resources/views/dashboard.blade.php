<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            {{ __('Dashboard') }}
        </h1>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <p class="text-slate-700 mb-5">{{ __("You're logged in!") }}</p>
            <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-800 text-sm font-semibold rounded-lg hover:bg-slate-700 transition !text-white">
                {{ __('View my orders') }} →
            </a>
        </div>
    </div>
</x-app-layout>
