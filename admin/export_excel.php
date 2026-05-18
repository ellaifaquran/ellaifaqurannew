<?php
require_once __DIR__ . '/../app/auth.php';
require_admin();

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

$sql = 'SELECT r.*, p.payment_status, p.transaction_status, p.payment_type, p.settlement_time FROM registrations r LEFT JOIN payments p ON p.registration_id = r.id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY r.created_at DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$filename = 'pendaftaran-ellaifa-' . date('Ymd-His') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo "\xEF\xBB\xBF"; // BOM supaya Excel membaca UTF-8 dengan benar.
$out = fopen('php://output', 'w');
fputcsv($out, [
    'Tanggal Daftar', 'Order ID', 'Nama Murid', 'Nama Wali', 'Email', 'WhatsApp', 'Program', 'Nominal',
    'Cabang', 'Jadwal Minat', 'Sumber Info', 'PIC', 'Status Pendaftaran', 'Status Pembayaran',
    'Midtrans Status', 'Metode Pembayaran', 'Settlement Time', 'Catatan'
]);

foreach ($rows as $row) {
    fputcsv($out, [
        $row['created_at'],
        $row['order_id'],
        $row['student_name'],
        $row['parent_name'],
        $row['email'],
        $row['phone'],
        $row['program_name'],
        $row['amount'],
        $row['branch'],
        $row['schedule_preference'],
        $row['source_info'],
        $row['pic_name'],
        status_label($row['status']),
        payment_label($row['payment_status']),
        $row['transaction_status'],
        $row['payment_type'],
        $row['settlement_time'],
        $row['notes'],
    ]);
}

fclose($out);
exit;
