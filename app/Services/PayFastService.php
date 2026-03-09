<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PayFast\Auth as PayFastAuth;

/**
 * Phase 9 – PayFast service
 *
 * WHY: Payment gateway details (URLs, signature, parameter order) live here so
 * controllers stay simple and we can test or swap gateways later. PayFast
 * requires a form POST to their process URL with a specific parameter order
 * and an MD5 signature. We use the official PayFast PHP SDK's Auth::generateSignature()
 * so our signature matches PayFast's expectations exactly.
 * See: https://developers.payfast.co.za/docs and payfast/payfast-php-sdk (Auth.php).
 */
class PayFastService
{
    private function baseUrl(): string
    {
        $mode = Config::get('services.payfast.mode', 'sandbox');

        return $mode === 'production'
            ? 'https://www.payfast.co.za/eng/process'
            : 'https://sandbox.payfast.co.za/eng/process';
    }

    /**
     * Build form data for a POST to PayFast (action URL + hidden fields).
     * Parameter order and signature follow the official PayFast PHP SDK
     * (Auth::generateSignature and CustomIntegration).
     *
     * @return array{action: string, fields: array<string, string>}
     */
    public function getPaymentFormData(Order $order, User $user): array
    {
        $merchantId = Config::get('services.payfast.merchant_id');
        $merchantKey = Config::get('services.payfast.merchant_key');
        $passphrase = Config::get('services.payfast.passphrase');
        // Per-order return URL so we know which order the user paid when they land back
        $baseReturn = rtrim(Config::get('services.payfast.return_url', ''), '?&');
        $returnUrl = $baseReturn . (str_contains($baseReturn, '?') ? '&' : '?') . 'order_id=' . $order->id;
        $cancelUrl = Config::get('services.payfast.cancel_url');
        $notifyUrl = Config::get('services.payfast.notify_url');

        $amount = number_format((float) $order->card_amount, 2, '.', '');
        $itemName = 'Order #' . $order->id . ' - ' . Config::get('app.name');
        $mPaymentId = (string) $order->id;

        $nameParts = explode(' ', trim($user->name), 2);
        $firstName = $nameParts[0] ?? 'Customer';
        $lastName = $nameParts[1] ?? '';

        // Order must match PayFast SDK Auth::$fields so signature iteration order is correct
        $data = [
            'merchant_id' => trim((string) $merchantId),
            'merchant_key' => trim((string) $merchantKey),
            'return_url' => trim((string) $returnUrl),
            'cancel_url' => trim((string) $cancelUrl),
            'notify_url' => trim((string) $notifyUrl),
            'name_first' => trim((string) $firstName),
            'name_last' => trim((string) $lastName),
            'email_address' => trim((string) $user->email),
            'm_payment_id' => trim((string) $mPaymentId),
            'amount' => $amount,
            'item_name' => trim((string) $itemName),
        ];
        $data = array_filter($data, fn ($v) => $v !== '');

        $passphraseForSignature = ($passphrase !== null && $passphrase !== '') ? $passphrase : null;
        $signature = PayFastAuth::generateSignature($data, $passphraseForSignature);
        $data['signature'] = $signature;

        $signatureString = $this->getSignatureStringForDebug($data, $passphraseForSignature);
        if (Config::get('app.debug')) {
            Log::debug('PayFast signature debug', [
                'string_used' => $signatureString,
                'signature_sent' => $signature,
            ]);
        }

        return [
            'action' => $this->baseUrl(),
            'fields' => $data,
            'signature_debug' => Config::get('app.debug') ? ['string_used' => $signatureString, 'signature_sent' => $signature] : null,
        ];
    }

    /**
     * Replicate SDK signature string building for debug output only (so you can paste into PayFast Signature troubleshooter).
     */
    private function getSignatureStringForDebug(array $data, ?string $passphrase): string
    {
        $fields = [
            'merchant_id', 'merchant_key', 'return_url', 'cancel_url', 'notify_url', 'notify_method',
            'name_first', 'name_last', 'email_address', 'cell_number', 'm_payment_id', 'amount', 'item_name',
            'item_description', 'custom_int1', 'custom_int2', 'custom_int3', 'custom_int4', 'custom_int5',
            'custom_str1', 'custom_str2', 'custom_str3', 'custom_str4', 'custom_str5',
            'email_confirmation', 'confirmation_address', 'currency', 'payment_method', 'subscription_type',
            'passphrase', 'billing_date', 'recurring_amount', 'frequency', 'cycles',
            'subscription_notify_email', 'subscription_notify_webhook', 'subscription_notify_buyer',
        ];
        $sortAttributes = array_filter($data, fn ($key) => in_array($key, $fields), ARRAY_FILTER_USE_KEY);
        if ($passphrase !== null && $passphrase !== '') {
            $sortAttributes['passphrase'] = urlencode(trim($passphrase));
        }
        $parts = [];
        foreach ($sortAttributes as $attribute => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            $encoded = ($attribute === 'passphrase') ? $value : urlencode(trim((string) $value));
            $parts[] = $attribute . '=' . $encoded;
        }

        return implode('&', $parts);
    }

    /**
     * Verify that an ITN (webhook) request signature matches our computed signature.
     * PayFast ITN uses alphabetical key order; we use the SDK's generateApiSignature.
     */
    public function validateNotifySignature(array $postData): bool
    {
        $received = $postData['signature'] ?? '';
        $postData = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $postData);
        $passphrase = Config::get('services.payfast.passphrase');
        $expected = PayFastAuth::generateApiSignature($postData, $passphrase);

        return hash_equals($expected, $received);
    }
}
