<p>{{ __('Hello :name,', ['name' => $order->user->name]) }}</p>
<p>{{ __('Your order #:id has been confirmed and paid.', ['id' => $order->id]) }}</p>
<p>{{ __('Total:') }} R{{ number_format($order->total_amount, 2) }}</p>
@if ((float) $order->rewards_used > 0)
    <p>{{ __('Rewards used:') }} R{{ number_format($order->rewards_used, 2) }}</p>
@endif
<p>{{ __('Thank you for your order.') }}</p>
