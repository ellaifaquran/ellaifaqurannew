<?php
require_once __DIR__ . '/app/midtrans.php';

header('Content-Type: application/json');

$rawBody = file_get_contents('php://input') ?: '';
$payload = json_decode($rawBody, true);

try {
    if (!is_array($payload)) {
        throw new RuntimeException('Invalid JSON payload');
    }

    $orderId = (string) ($payload['order_id'] ?? '');
    if ($orderId === '') {
        throw new RuntimeException('order_id kosong');
    }

    $pdo = db();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('INSERT INTO webhook_logs (order_id, payload, received_at) VALUES (?, ?, NOW())');
    $stmt->execute([$orderId, $rawBody]);

    if (!midtrans_verify_signature($payload)) {
        $pdo->commit();
        http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'Invalid signature']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id FROM registrations WHERE order_id = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $registration = $stmt->fetch();

    if (!$registration) {
        $pdo->commit();
        http_response_code(404);
        echo json_encode(['ok' => false, 'message' => 'Order not found']);
        exit;
    }

    $paymentStatus = map_midtrans_payment_status($payload);
    $registrationStatus = map_registration_status_from_payment($paymentStatus);

    $stmt = $pdo->prepare('UPDATE payments SET payment_status = ?, transaction_status = ?, fraud_status = ?, payment_type = ?, transaction_id = ?, settlement_time = ?, expiry_time = ?, raw_notification = ?, updated_at = NOW() WHERE order_id = ?');
    $stmt->execute([
        $paymentStatus,
        $payload['transaction_status'] ?? null,
        $payload['fraud_status'] ?? null,
        $payload['payment_type'] ?? null,
        $payload['transaction_id'] ?? null,
        $payload['settlement_time'] ?? null,
        $payload['expiry_time'] ?? null,
        $rawBody,
        $orderId,
    ]);

    $stmt = $pdo->prepare('UPDATE registrations SET status = ?, updated_at = NOW() WHERE order_id = ? AND status NOT IN ("active", "cancelled")');
    $stmt->execute([$registrationStatus, $orderId]);

    $pdo->commit();
    echo json_encode(['ok' => true, 'order_id' => $orderId, 'payment_status' => $paymentStatus]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
