{{--
  Phase 12 – Order detail

  WHY: We receive $order (with items and product eager-loaded) from OrderController@show.
  Shows line items, totals, status. "Mark as completed" only when status is paid (manual fulfilment).
--}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            {{ __('Order #:id', ['id' => $order->id]) }}
        </h1>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-auth-session-status class="mb-6" :status="session('status')" />

        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                    @php
                        $statusClass = 'bg-amber-100 text-amber-800';
                        if ($order->status === 'completed') { $statusClass = 'bg-emerald-100 text-emerald-800'; }
                        elseif ($order->status === 'paid') { $statusClass = 'bg-sky-100 text-sky-800'; }
                    @endphp
                    <span class="inline-block mt-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    @if ($order->status === 'pending')
                        <form method="POST" action="{{ route('orders.mark-paid', $order) }}" class="inline" onsubmit="return confirm('{{ __('Mark this order as paid? Only do this if you have confirmed payment (e.g. in PayFast).') }}');">
                            @csrf
                            @method('PATCH')
                            <x-primary-button type="submit" class="!bg-sky-600 hover:!bg-sky-700">{{ __('Mark as paid') }}</x-primary-button>
                        </form>
                    @endif
                    @if ($order->status === 'paid')
                        <form method="POST" action="{{ route('orders.complete', $order) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <x-primary-button type="submit">{{ __('Mark as completed') }}</x-primary-button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Payment summary --}}
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">{{ __('Payment summary') }}</h3>
                <dl class="space-y-1 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-600">{{ __('Order total') }}</dt><dd class="font-medium text-slate-900">R{{ number_format($order->total_amount, 2) }}</dd></div>
                    @if ((float) $order->rewards_used > 0)
                        <div class="flex justify-between"><dt class="text-slate-600">{{ __('Rewards / voucher used') }}</dt><dd class="font-medium text-slate-900">− R{{ number_format($order->rewards_used, 2) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-slate-600">{{ __('Paid by card') }}</dt><dd class="font-medium text-slate-900">R{{ number_format($order->card_amount, 2) }}</dd></div>
                    <div class="flex justify-between pt-1 border-t border-slate-200 mt-2">
                        <dt class="text-slate-600">{{ __('Payment status') }}</dt>
                        <dd><span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass ?? 'bg-amber-100 text-amber-800' }}">{{ ucfirst($order->status) }}</span></dd>
                    </div>
                </dl>
                <p class="text-xs text-slate-500 mt-2">{{ __('Status is updated automatically when PayFast confirms your payment.') }}</p>
                @if ($order->status === 'pending')
                    <p class="text-xs text-slate-500 mt-1">{{ __('If you have already paid, PayFast may still be notifying us. When testing locally, you can use "Mark as paid" after confirming in PayFast.') }}</p>
                @endif
                @if ($order->payfast_reference)
                    <p class="text-xs text-slate-500 mt-1">{{ __('Payment reference') }}: {{ $order->payfast_reference }}</p>
                @endif
            </div>

            <ul class="divide-y divide-slate-200">
                @foreach($order->items as $item)
                    <li class="p-6 flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $item->product->title ?? __('Product') }}</p>
                            <p class="text-sm text-slate-500 mt-0.5">{{ __('Qty') }}: {{ $item->quantity }} × R{{ number_format($item->price_at_purchase, 2) }}</p>
                        </div>
                        <p class="font-semibold text-slate-900">R{{ number_format($item->quantity * (float) $item->price_at_purchase, 2) }}</p>
                    </li>
                @endforeach
            </ul>

            <div class="p-6 bg-slate-50/80 border-t border-slate-200">
                <p class="text-lg font-bold text-slate-900">{{ __('Total') }}: R{{ number_format($order->total_amount, 2) }}</p>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('orders.index') }}" class="text-slate-600 hover:text-slate-900 font-medium text-sm">
                ← {{ __('Back to orders') }}
            </a>
        </div>
    </div>
</x-app-layout>
