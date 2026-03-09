{{--
  Phase 7 – Cart index view

  WHY: We receive $carts (current user's cart rows with product eager-loaded)
  and $total from the controller. Each row shows product name, price,
  quantity (editable via form with method PATCH), line total, and a
  remove form (method DELETE with @method). We use route('checkout') for
  the checkout link (Phase 8); empty cart shows a message and link to products.
--}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            {{ __('Your cart') }}
        </h1>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-auth-session-status class="mb-6" :status="session('status')" />

        @if($carts->isEmpty())
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-10 text-center">
                <p class="text-slate-600">{{ __('Your cart is empty.') }}</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center mt-5 px-4 py-2.5 bg-slate-800 text-sm font-semibold rounded-lg hover:bg-slate-700 transition !text-white">
                    {{ __('Browse products') }}
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
                <ul class="divide-y divide-slate-200">
                    @foreach($carts as $cart)
                        @php $lineTotal = $cart->quantity * (float) $cart->product->price; @endphp
                        <li class="p-6 flex flex-wrap items-center gap-4 sm:gap-6">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-900">{{ $cart->product->title }}</p>
                                <p class="text-sm text-slate-500 mt-0.5">R{{ number_format($cart->product->price, 2) }} each</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('cart.update', $cart) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label for="qty-{{ $cart->id }}" class="sr-only">{{ __('Quantity') }}</label>
                                    <input type="number" name="quantity" id="qty-{{ $cart->id }}" value="{{ $cart->quantity }}" min="1" class="rounded-lg border-slate-300 shadow-sm w-20 text-sm focus:ring-slate-500 focus:border-slate-500">
                                    <x-primary-button type="submit" class="!py-1.5 !px-3 !text-xs">{{ __('Update') }}</x-primary-button>
                                </form>
                                <form method="POST" action="{{ route('cart.destroy', $cart) }}" class="inline" onsubmit="return confirm('{{ __('Remove this item?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit" class="!py-1.5 !px-3 !text-xs">{{ __('Remove') }}</x-danger-button>
                                </form>
                            </div>
                            <div class="w-full sm:w-auto sm:min-w-[6rem] text-right font-semibold text-slate-900">
                                R{{ number_format($lineTotal, 2) }}
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="p-6 bg-slate-50/80 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-lg font-bold text-slate-900">
                        {{ __('Total:') }} R{{ number_format($total, 2) }}
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white border border-slate-400 text-sm font-medium rounded-lg hover:bg-slate-100 transition !text-slate-900">
                            {{ __('Continue shopping') }}
                        </a>
                        <a href="{{ route('checkout') }}" class="inline-flex items-center px-5 py-2.5 bg-slate-800 text-sm font-semibold rounded-lg hover:bg-slate-700 transition !text-white">
                            {{ __('Proceed to checkout') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
