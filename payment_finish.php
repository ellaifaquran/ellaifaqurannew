<?php
require_once __DIR__ . '/app/helpers.php';

$orderId = trim((string) ($_GET['order_id'] ?? ''));
$registration = null;
$payment = null;

if ($orderId !== '') {
    $stmt = db()->prepare('SELECT r.*, p.payment_status, p.transaction_status, p.payment_type, p.settlement_time FROM registrations r LEFT JOIN payments p ON p.registration_id = r.id WHERE r.order_id = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $registration = $stmt->fetch();
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Status Pendaftaran</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <main class="container narrow">
    <div class="card center">
      <?php if (!$registration): ?>
        <h1>Data tidak ditemukan</h1>
        <p>Order ID tidak ditemukan. Silakan hubungi admin.</p>
        <a class="btn" href="index.php">Kembali</a>
      <?php else: ?>
        <p class="eyebrow">Order ID: <?= h($registration['order_id']) ?></p>
        <h1>Status Pendaftaran</h1>
        <p><strong><?= h($registration['student_name']) ?></strong> - <?= h($registration['program_name']) ?></p>
        <p class="price"><?= h(rupiah($registration['amount'])) ?></p>
        <div class="status-box">
          <div>Status Pendaftaran</div>
          <strong><?= h(status_label($registration['status'])) ?></strong>
        </div>
        <div class="status-box">
          <div>Status Pembayaran</div>
          <strong><?= h(payment_label($registration['payment_status'] ?? null)) ?></strong>
        </div>
        <p class="muted small">Catatan: status final pembayaran mengikuti notifikasi/webhook Midtrans yang masuk ke server.</p>
        <a class="btn" href="index.php">Daftar Murid Lain</a>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
