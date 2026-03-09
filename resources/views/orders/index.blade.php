{{--
  Phase 12 – Order history (list)

  WHY: We receive $orders (current user's orders, newest first) from OrderController@index.
  Each row shows order id, total, status (pending/paid/completed), date, and link to detail.
--}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            {{ __('My orders') }}
        </h1>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-auth-session-status class="mb-6" :status="session('status')" />

        @if($orders->isEmpty())
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-10 text-center">
                <p class="text-slate-600">{{ __('You have no orders yet.') }}</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center mt-5 px-4 py-2.5 bg-slate-800 text-sm font-semibold rounded-lg hover:bg-slate-700 transition !text-white">
                    {{ __('Browse products') }}
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
                <ul class="divide-y divide-slate-200">
                    @foreach($orders as $order)
                        <li class="p-6 flex flex-wrap items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                            <div>
                                <p class="font-semibold text-slate-900">
                                    {{ __('Order #:id', ['id' => $order->id]) }}
                                    <span class="ml-2 text-sm font-normal text-slate-500">
                                        {{ $order->created_at->format('Y-m-d H:i') }}
                                    </span>
                                </p>
                                <p class="text-sm text-slate-600 mt-1">
                                    {{ __('Total') }}: R{{ number_format($order->total_amount, 2) }}
                                    · {{ $order->items_count }} {{ __('item(s)') }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    @if ((float) $order->rewards_used > 0)
                                        {{ __('Card') }}: R{{ number_format($order->card_amount, 2) }}
                                        · {{ __('Rewards') }}: R{{ number_format($order->rewards_used, 2) }}
                                    @else
                                        {{ __('Card') }}: R{{ number_format($order->card_amount, 2) }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                @php
                                    $statusClass = 'bg-amber-100 text-amber-800';
                                    if ($order->status === 'completed') { $statusClass = 'bg-emerald-100 text-emerald-800'; }
                                    elseif ($order->status === 'paid') { $statusClass = 'bg-sky-100 text-sky-800'; }
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <a href="{{ route('orders.show', $order) }}" class="text-slate-700 font-medium text-sm hover:text-slate-900 underline">
                                    {{ __('View') }}
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-app-layout>
