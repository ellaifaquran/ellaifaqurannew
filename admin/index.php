<?php
require_once __DIR__ . '/../app/auth.php';
$admin = require_admin();

$status = trim((string) ($_GET['status'] ?? ''));
$q = trim((string) ($_GET['q'] ?? ''));

$where = [];
$params = [];
if ($status !== '') {
    $where[] = 'r.status = ?';
    $params[] = $status;
}
if ($q !== '') {
    $where[] = '(r.student_name LIKE ? OR r.parent_name LIKE ? OR r.phone LIKE ? OR r.order_id LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like);
}

$sql = 'SELECT r.*, p.payment_status, p.payment_type, p.settlement_time FROM registrations r LEFT JOIN payments p ON p.registration_id = r.id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY r.created_at DESC LIMIT 300';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$registrations = $stmt->fetchAll();

$statuses = ['new','waiting_payment','paid','contacted','trial','active','cancelled','payment_failed','payment_expired'];
$queryString = http_build_query(['status' => $status, 'q' => $q]);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel Admin - Pendaftaran</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <main class="container wide">
    <header class="admin-header">
      <div>
        <p class="eyebrow">Panel Admin</p>
        <h1>Data Pendaftaran</h1>
      </div>
      <div class="admin-actions">
        <span class="muted small">Halo, <?= h($admin['name']) ?></span>
        <a class="btn ghost" href="export_excel.php?<?= h($queryString) ?>">Export CSV/Excel</a>
        <a class="btn" href="logout.php">Logout</a>
      </div>
    </header>

    <form class="card filters" method="get">
      <input type="search" name="q" value="<?= h($q) ?>" placeholder="Cari nama, WA, order ID">
      <select name="status">
        <option value="">Semua status</option>
        <?php foreach ($statuses as $s): ?>
          <option value="<?= h($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= h(status_label($s)) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn primary" type="submit">Filter</button>
      <a class="btn ghost" href="index.php">Reset</a>
    </form>

    <div class="card table-card">
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Murid</th>
            <th>Wali</th>
            <th>WA</th>
            <th>Program</th>
            <th>Nominal</th>
            <th>Status</th>
            <th>Bayar</th>
            <th>PIC</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$registrations): ?>
            <tr><td colspan="10" class="muted">Belum ada data.</td></tr>
          <?php endif; ?>
          <?php foreach ($registrations as $row): ?>
            <tr>
              <td><?= h(date('d M Y H:i', strtotime($row['created_at']))) ?></td>
              <td><strong><?= h($row['student_name']) ?></strong><br><span class="muted small"><?= h($row['order_id']) ?></span></td>
              <td><?= h($row['parent_name']) ?></td>
              <td><a href="https://wa.me/<?= h($row['phone']) ?>" target="_blank" rel="noopener"><?= h($row['phone']) ?></a></td>
              <td><?= h($row['program_name']) ?><br><span class="muted small"><?= h($row['branch']) ?></span></td>
              <td><?= h(rupiah($row['amount'])) ?></td>
              <td><span class="badge"><?= h(status_label($row['status'])) ?></span></td>
              <td><?= h(payment_label($row['payment_status'])) ?></td>
              <td><?= h($row['pic_name'] ?: '-') ?></td>
              <td><a class="btn small" href="detail.php?id=<?= h($row['id']) ?>">Detail</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>
