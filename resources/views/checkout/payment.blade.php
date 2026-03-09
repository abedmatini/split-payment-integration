{{--
  Phase 8/9 – Payment step (after order created)

  When PayFast is configured we receive $paymentFormData (action URL + fields).
  We render a form that POSTs to PayFast and auto-submit so the user is sent
  to the payment gateway. When not configured we show a fallback message.
--}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            {{ __('Complete payment') }}
        </h1>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <p class="text-slate-600 mb-2">{{ __('Order #:id', ['id' => $order->id]) }}</p>
            <p class="text-xl font-bold text-slate-900 mb-2">
                {{ __('Amount to pay:') }} R{{ number_format($order->card_amount, 2) }}
            </p>
            @if ((float) $order->rewards_used > 0)
                <p class="text-sm text-slate-600 mb-6">
                    {{ __('Rewards applied:') }} R{{ number_format($order->rewards_used, 2) }}
                    ({{ __('Total was') }} R{{ number_format($order->total_amount, 2) }})
                </p>
            @endif

            @if(isset($paymentFormData) && $paymentFormData)
                @if(!empty($paymentFormData['signature_debug']))
                    <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm">
                        <p class="font-semibold text-amber-900 mb-2">Debug (APP_DEBUG=true) – use this to compare with PayFast docs or an MD5 checker:</p>
                        <p class="text-amber-800 break-all mb-1"><strong>String used for signature:</strong><br><code class="text-xs">{{ $paymentFormData['signature_debug']['string_used'] ?? '' }}</code></p>
                        <p class="text-amber-800"><strong>Signature sent:</strong> <code>{{ $paymentFormData['signature_debug']['signature_sent'] ?? '' }}</code></p>
                        <p class="mt-2 text-amber-700">Auto-submit is disabled in debug mode. Click the button below to go to PayFast.</p>
                    </div>
                @else
                    <p class="text-sm text-slate-600 mb-6">{{ __('Redirecting to PayFast to complete payment…') }}</p>
                @endif
                <form id="payfast-form" method="POST" action="{{ $paymentFormData['action'] }}">
                    @foreach($paymentFormData['fields'] as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endforeach
                    <x-primary-button type="submit" class="!px-6 !py-3 !text-base">{{ __('Pay with PayFast') }}</x-primary-button>
                </form>
                @if(empty($paymentFormData['signature_debug']))
                <script>
                    document.getElementById('payfast-form').submit();
                </script>
                @endif
            @else
                <p class="text-sm text-slate-500 mt-6">
                    {{ __('Configure PAYFAST_MERCHANT_ID and PAYFAST_MERCHANT_KEY in .env to enable PayFast. Your order is saved and pending payment.') }}
                </p>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center mt-6 px-4 py-2.5 bg-slate-800 text-sm font-semibold rounded-lg hover:bg-slate-700 transition !text-white">
                    {{ __('Back to dashboard') }}
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
