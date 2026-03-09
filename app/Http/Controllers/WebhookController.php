<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Services\PayFastService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

/**
 * Phase 10 – Webhook controller
 *
 * WHY: PayFast sends an ITN (Instant Transaction Notification) POST to our
 * notify_url when payment is complete. We verify the signature so we only
 * trust genuine PayFast callbacks. We find the order by m_payment_id (our
 * order id), update status to paid, deduct rewards, and send email. We return
 * 200 quickly so PayFast stops retrying. Idempotency: we only process when
 * order status is still pending (so duplicate webhooks don't double-deduct).
 */
class WebhookController extends Controller
{
    public function __construct(
        private PayFastService $payFastService
    ) {}

    /**
     * Handle PayFast ITN (notify_url) callback.
     */
    public function payfast(Request $request): Response
    {
        $data = $request->all();

        if (! $this->payFastService->validateNotifySignature($data)) {
            return response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        $paymentStatus = $data['payment_status'] ?? '';
        $mPaymentId = $data['m_payment_id'] ?? '';

        if ($paymentStatus !== 'COMPLETE' || $mPaymentId === '') {
            return response('OK', 200);
        }

        $order = Order::find($mPaymentId);
        if (! $order || $order->status !== 'pending') {
            return response('OK', 200);
        }

        $order->status = 'paid';
        $order->payfast_reference = $data['pf_payment_id'] ?? null;
        $order->save();

        $rewardsUsed = (float) $order->rewards_used;
        if ($rewardsUsed > 0) {
            $user = $order->user;
            $user->rewards_balance = max(0, (float) $user->rewards_balance - $rewardsUsed);
            $user->save();
        }

        try {
            Mail::to($order->user->email)->send(new OrderConfirmation($order));
        } catch (\Throwable $e) {
            report($e);
        }

        return response('OK', 200);
    }
}
