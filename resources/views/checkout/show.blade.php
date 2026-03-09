{{--
  Phase 8 – Checkout form

  WHY: We receive $carts, $total, and $rewardsBalance from the controller.
  The form posts use_rewards (checkbox) so the server can compute
  rewards_used and card_amount when creating the order. We use a small
  script to update the displayed "rewards used" and "card amount" when
  the checkbox is toggled (UX); the real values are computed server-side
  in OrderService. All amounts are in ZAR.
--}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            {{ __('Checkout') }}
        </h1>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <p class="text-sm text-slate-600 mb-6">
                {{ __('Your rewards balance:') }} <strong class="text-slate-900">R{{ number_format($rewardsBalance, 2) }}</strong>
            </p>

            <ul class="divide-y divide-slate-200 mb-6">
                @foreach($carts as $cart)
                    @php $lineTotal = $cart->quantity * (float) $cart->product->price; @endphp
                    <li class="py-3 flex justify-between text-slate-700">
                        <span>{{ $cart->product->title }} &times; {{ $cart->quantity }}</span>
                        <span class="font-medium">R{{ number_format($lineTotal, 2) }}</span>
                    </li>
                @endforeach
            </ul>

            <p class="text-lg font-bold text-slate-900 mb-6">
                {{ __('Cart total:') }} R{{ number_format($total, 2) }}
            </p>

            <form method="POST" action="{{ route('checkout.store') }}" class="space-y-5">
                @csrf

                <div class="flex items-center rounded-lg bg-slate-50 p-4">
                    <input type="checkbox" name="use_rewards" id="use_rewards" value="1"
                           class="h-4 w-4 rounded border-slate-300 text-slate-700 focus:ring-slate-500"
                           {{ $rewardsBalance >= 0.01 && $total >= 0.01 ? '' : 'disabled' }}>
                    <label for="use_rewards" class="ms-3 text-sm font-medium text-slate-700">{{ __('Use my rewards balance') }}</label>
                </div>

                <div id="split-summary" class="hidden rounded-xl bg-emerald-50 border border-emerald-200/80 p-4 text-sm">
                    <p class="text-emerald-800">{{ __('Rewards applied:') }} <strong id="rewards-used">R0.00</strong></p>
                    <p class="mt-1 text-emerald-800">{{ __('Amount to pay by card:') }} <strong id="card-amount">R{{ number_format($total, 2) }}</strong></p>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="{{ route('cart.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white border border-slate-400 text-sm font-medium rounded-lg hover:bg-slate-100 transition !text-slate-900">
                        {{ __('Back to cart') }}
                    </a>
                    <x-primary-button type="submit">
                        {{ __('Proceed to payment') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var total = {{ json_encode((float) $total) }};
            var balance = {{ json_encode((float) $rewardsBalance) }};
            var cb = document.getElementById('use_rewards');
            var summary = document.getElementById('split-summary');
            var rewardsEl = document.getElementById('rewards-used');
            var cardEl = document.getElementById('card-amount');

            function update() {
                if (cb && cb.checked) {
                    var used = Math.min(balance, total);
                    var card = total - used;
                    rewardsEl.textContent = 'R' + used.toFixed(2);
                    cardEl.textContent = 'R' + card.toFixed(2);
                    summary.classList.remove('hidden');
                } else {
                    if (rewardsEl) rewardsEl.textContent = 'R0.00';
                    if (cardEl) cardEl.textContent = 'R' + total.toFixed(2);
                    if (summary) summary.classList.add('hidden');
                }
            }

            if (cb) {
                cb.addEventListener('change', update);
                update();
            }
        })();
    </script>
</x-app-layout>
