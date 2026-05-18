<?php
require_once __DIR__ . '/../app/auth.php';
$admin = require_admin();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('index.php');
}

$statuses = ['new','waiting_payment','paid','contacted','trial','active','cancelled','payment_failed','payment_expired'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $newStatus = (string) ($_POST['status'] ?? '');
    $picName = trim((string) ($_POST['pic_name'] ?? ''));
    $followupNote = trim((string) ($_POST['followup_note'] ?? ''));

    if (!in_array($newStatus, $statuses, true)) {
        $newStatus = 'new';
    }

    $pdo = db();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('UPDATE registrations SET status = ?, pic_name = ?, updated_at = NOW() WHERE id = ?');
    $stmt->execute([$newStatus, $picName, $id]);

    if ($followupNote !== '') {
        $stmt = $pdo->prepare('INSERT INTO followups (registration_id, admin_id, status, note, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$id, $admin['id'], $newStatus, $followupNote]);
    }
    $pdo->commit();
    $message = 'Data berhasil diperbarui.';
}

$stmt = db()->prepare('SELECT r.*, p.payment_status, p.transaction_status, p.fraud_status, p.payment_type, p.transaction_id, p.settlement_time, p.expiry_time FROM registrations r LEFT JOIN payments p ON p.registration_id = r.id WHERE r.id = ? LIMIT 1');
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) {
    redirect('index.php');
}

$stmt = db()->prepare('SELECT f.*, a.name AS admin_name FROM followups f LEFT JOIN admin_users a ON a.id = f.admin_id WHERE f.registration_id = ? ORDER BY f.created_at DESC');
$stmt->execute([$id]);
$followups = $stmt->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Pendaftaran</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <main class="container wide">
    <header class="admin-header">
      <div>
        <a class="muted small" href="index.php">← Kembali</a>
        <h1>Detail Pendaftaran</h1>
      </div>
      <a class="btn" href="logout.php">Logout</a>
    </header>

    <?php if ($message): ?><div class="alert success"><?= h($message) ?></div><?php endif; ?>

    <div class="grid two align-start">
      <section class="card">
        <p class="eyebrow">Order ID</p>
        <h2><?= h($row['order_id']) ?></h2>
        <dl class="details">
          <dt>Nama Murid</dt><dd><?= h($row['student_name']) ?></dd>
          <dt>Nama Wali</dt><dd><?= h($row['parent_name']) ?></dd>
          <dt>Email</dt><dd><?= h($row['email']) ?></dd>
          <dt>WhatsApp</dt><dd><a href="https://wa.me/<?= h($row['phone']) ?>" target="_blank" rel="noopener"><?= h($row['phone']) ?></a></dd>
          <dt>Program</dt><dd><?= h($row['program_name']) ?></dd>
          <dt>Nominal</dt><dd><?= h(rupiah($row['amount'])) ?></dd>
          <dt>Cabang/Area</dt><dd><?= h($row['branch']) ?></dd>
          <dt>Jadwal Minat</dt><dd><?= h($row['schedule_preference'] ?: '-') ?></dd>
          <dt>Sumber Info</dt><dd><?= h($row['source_info'] ?: '-') ?></dd>
          <dt>Catatan Form</dt><dd><?= nl2br(h($row['notes'] ?: '-')) ?></dd>
          <dt>Dibuat</dt><dd><?= h(date('d M Y H:i', strtotime($row['created_at']))) ?></dd>
        </dl>
      </section>

      <section class="card">
        <h2>Status & Follow-up</h2>
        <form class="form" method="post">
          <?= csrf_field() ?>
          <label>Status
            <select name="status">
              <?php foreach ($statuses as $s): ?>
                <option value="<?= h($s) ?>" <?= $row['status'] === $s ? 'selected' : '' ?>><?= h(status_label($s)) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>PIC
            <input type="text" name="pic_name" value="<?= h($row['pic_name']) ?>" maxlength="120" placeholder="Nama PIC">
          </label>
          <label>Catatan Follow-up Baru
            <textarea name="followup_note" rows="5" placeholder="Contoh: Sudah WA wali, menunggu konfirmasi jadwal."></textarea>
          </label>
          <button class="btn primary" type="submit">Simpan Update</button>
        </form>

        <hr>
        <h3>Status Pembayaran</h3>
        <dl class="details compact">
          <dt>Payment Status</dt><dd><?= h(payment_label($row['payment_status'])) ?></dd>
          <dt>Midtrans Status</dt><dd><?= h($row['transaction_status'] ?: '-') ?></dd>
          <dt>Payment Type</dt><dd><?= h($row['payment_type'] ?: '-') ?></dd>
          <dt>Transaction ID</dt><dd><?= h($row['transaction_id'] ?: '-') ?></dd>
          <dt>Settlement</dt><dd><?= h($row['settlement_time'] ?: '-') ?></dd>
          <dt>Expiry</dt><dd><?= h($row['expiry_time'] ?: '-') ?></dd>
        </dl>
        <?php if (!empty($row['midtrans_redirect_url']) && $row['payment_status'] !== 'paid'): ?>
          <a class="btn ghost" href="<?= h($row['midtrans_redirect_url']) ?>" target="_blank" rel="noopener">Buka Link Pembayaran</a>
        <?php endif; ?>
      </section>
    </div>

    <section class="card">
      <h2>Riwayat Follow-up</h2>
      <?php if (!$followups): ?><p class="muted">Belum ada catatan follow-up.</p><?php endif; ?>
      <?php foreach ($followups as $f): ?>
        <div class="timeline-item">
          <strong><?= h(status_label($f['status'])) ?></strong>
          <span class="muted small">oleh <?= h($f['admin_name'] ?: 'Admin') ?> · <?= h(date('d M Y H:i', strtotime($f['created_at']))) ?></span>
          <p><?= nl2br(h($f['note'])) ?></p>
        </div>
      <?php endforeach; ?>
    </section>
  </main>
</body>
</html>
