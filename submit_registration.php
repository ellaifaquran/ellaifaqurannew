<?php
require_once __DIR__ . '/app/midtrans.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

function post_value(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

$studentName = post_value('student_name');
$parentName = post_value('parent_name');
$email = post_value('email');
$phone = normalize_phone(post_value('phone'));
$programId = (int) post_value('program_id');
$branch = post_value('branch') ?: (string) app_config('registration.default_branch', 'Online');
$schedulePreference = post_value('schedule_preference');
$sourceInfo = post_value('source_info');
$notes = post_value('notes');

$errors = [];
if ($studentName === '') $errors[] = 'Nama murid wajib diisi.';
if ($parentName === '') $errors[] = 'Nama orang tua/wali wajib diisi.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
if ($phone === '') $errors[] = 'Nomor WhatsApp wajib diisi.';

$program = get_program($programId);
if (!$program) $errors[] = 'Program tidak valid.';

if ($errors) {
    http_response_code(422);
    ?>
    <!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Data belum lengkap</title><link rel="stylesheet" href="assets/style.css"></head><body><main class="container narrow"><div class="card"><h1>Data belum lengkap</h1><ul><?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul><a class="btn" href="index.php">Kembali ke Form</a></div></main></body></html>
    <?php
    exit;
}

$orderId = 'ELLAIFA-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
$amount = (int) $program['price'];

try {
    $pdo = db();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('INSERT INTO registrations (order_id, student_name, parent_name, email, phone, program_id, program_name, amount, branch, schedule_preference, source_info, notes, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([$orderId, $studentName, $parentName, $email, $phone, $program['id'], $program['name'], $amount, $branch, $schedulePreference, $sourceInfo, $notes, 'waiting_payment']);
    $registrationId = (int) $pdo->lastInsertId();

    $stmt = $pdo->prepare('INSERT INTO payments (registration_id, order_id, gross_amount, payment_status, transaction_status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([$registrationId, $orderId, $amount, 'pending', 'pending']);

    $registration = [
        'id' => $registrationId,
        'order_id' => $orderId,
        'student_name' => $studentName,
        'parent_name' => $parentName,
        'email' => $email,
        'phone' => $phone,
        'program_id' => $program['id'],
        'program_name' => $program['name'],
        'amount' => $amount,
    ];

    $snap = midtrans_create_snap_transaction($registration);

    $stmt = $pdo->prepare('UPDATE registrations SET midtrans_snap_token = ?, midtrans_redirect_url = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$snap['token'], $snap['redirect_url'], $registrationId]);

    $pdo->commit();
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    ?>
    <!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Gagal membuat pembayaran</title><link rel="stylesheet" href="assets/style.css"></head><body><main class="container narrow"><div class="card"><h1>Gagal membuat pembayaran</h1><p>Sistem berhasil membaca form, tetapi belum bisa membuat transaksi Midtrans.</p><p class="muted small">Error teknis: <?= h($e->getMessage()) ?></p><a class="btn" href="index.php">Coba Lagi</a></div></main></body></html>
    <?php
    exit;
}

$clientKey = (string) app_config('midtrans.client_key');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lanjut Pembayaran</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="<?= h(midtrans_snap_js_url()) ?>" data-client-key="<?= h($clientKey) ?>"></script>
</head>
<body>
  <main class="container narrow">
    <div class="card center">
      <p class="eyebrow">Order ID: <?= h($orderId) ?></p>
      <h1>Pendaftaran Tersimpan</h1>
      <p>Silakan lanjutkan pembayaran untuk <strong><?= h($studentName) ?></strong>.</p>
      <p class="price"><?= h(rupiah($amount)) ?></p>
      <button id="pay-button" class="btn primary">Bayar Sekarang</button>
      <a class="btn ghost" href="<?= h($snap['redirect_url']) ?>">Buka Halaman Pembayaran</a>
      <p class="muted small">Jika popup tidak muncul, klik tombol “Buka Halaman Pembayaran”.</p>
    </div>
  </main>
  <script>
    const token = <?= json_encode($snap['token']) ?>;
    const fallbackUrl = <?= json_encode($snap['redirect_url']) ?>;

    function openSnap() {
      if (!window.snap) {
        window.location.href = fallbackUrl;
        return;
      }
      window.snap.pay(token, {
        onSuccess: function(){ window.location.href = 'payment_finish.php?order_id=' + encodeURIComponent(<?= json_encode($orderId) ?>); },
        onPending: function(){ window.location.href = 'payment_finish.php?order_id=' + encodeURIComponent(<?= json_encode($orderId) ?>); },
        onError: function(){ window.location.href = 'payment_finish.php?order_id=' + encodeURIComponent(<?= json_encode($orderId) ?>); },
        onClose: function(){ /* customer menutup popup, tetap di halaman ini */ }
      });
    }

    document.getElementById('pay-button').addEventListener('click', openSnap);
    setTimeout(openSnap, 600);
  </script>
</body>
</html>
