<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

function midtrans_snap_api_url(): string
{
    return app_config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
}

function midtrans_snap_js_url(): string
{
    return app_config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
}

function midtrans_create_snap_transaction(array $registration): array
{
    $serverKey = (string) app_config('midtrans.server_key');
    if ($serverKey === '' || str_contains($serverKey, 'ISI_SERVER_KEY')) {
        throw new RuntimeException('Server Key Midtrans belum diisi di app/config.php');
    }

    $payload = [
        'transaction_details' => [
            'order_id' => $registration['order_id'],
            'gross_amount' => (int) $registration['amount'],
        ],
        'item_details' => [[
            'id' => 'PROGRAM-' . $registration['program_id'],
            'price' => (int) $registration['amount'],
            'quantity' => 1,
            'name' => mb_substr($registration['program_name'], 0, 50),
        ]],
        'customer_details' => [
            'first_name' => mb_substr($registration['parent_name'], 0, 255),
            'email' => $registration['email'],
            'phone' => $registration['phone'],
        ],
        'callbacks' => [
            'finish' => app_url('payment_finish.php?order_id=' . rawurlencode((string) $registration['order_id'])),
        ],
    ];

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($serverKey . ':'),
    ];

    $notificationUrl = (string) app_config('midtrans.notification_url', '');
    if ($notificationUrl !== '') {
        // Tetap set Payment Notification URL di dashboard. Header ini membantu saat testing/subfolder.
        $headers[] = 'X-Append-Notification: ' . $notificationUrl;
    }

    $ch = curl_init(midtrans_snap_api_url());
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException('Gagal menghubungi Midtrans: ' . $curlError);
    }

    $decoded = json_decode($response, true);
    if ($httpCode < 200 || $httpCode >= 300 || !is_array($decoded)) {
        throw new RuntimeException('Midtrans error HTTP ' . $httpCode . ': ' . $response);
    }

    if (empty($decoded['token']) || empty($decoded['redirect_url'])) {
        throw new RuntimeException('Response Midtrans tidak berisi token/redirect_url: ' . $response);
    }

    return $decoded;
}

function midtrans_verify_signature(array $payload): bool
{
    $required = ['order_id', 'status_code', 'gross_amount', 'signature_key'];
    foreach ($required as $key) {
        if (!isset($payload[$key])) {
            return false;
        }
    }

    $serverKey = (string) app_config('midtrans.server_key');
    $expected = hash('sha512', (string) $payload['order_id'] . (string) $payload['status_code'] . (string) $payload['gross_amount'] . $serverKey);
    return hash_equals($expected, (string) $payload['signature_key']);
}

function map_midtrans_payment_status(array $payload): string
{
    $transactionStatus = (string) ($payload['transaction_status'] ?? '');
    $fraudStatus = (string) ($payload['fraud_status'] ?? '');

    if ($transactionStatus === 'capture') {
        return ($fraudStatus === 'challenge') ? 'challenge' : 'paid';
    }
    if ($transactionStatus === 'settlement') {
        return 'paid';
    }
    if ($transactionStatus === 'pending') {
        return 'pending';
    }
    if ($transactionStatus === 'expire') {
        return 'expired';
    }
    if ($transactionStatus === 'cancel') {
        return 'cancelled';
    }
    if (in_array($transactionStatus, ['deny', 'failure'], true)) {
        return 'failed';
    }
    if (in_array($transactionStatus, ['refund', 'partial_refund'], true)) {
        return 'refunded';
    }

    return $transactionStatus ?: 'unknown';
}

function map_registration_status_from_payment(string $paymentStatus): string
{
    return match ($paymentStatus) {
        'paid' => 'paid',
        'pending', 'challenge' => 'waiting_payment',
        'expired' => 'payment_expired',
        'cancelled', 'failed' => 'payment_failed',
        default => 'waiting_payment',
    };
}
