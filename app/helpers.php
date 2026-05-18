<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function rupiah(int|float|string $amount): string
{
    return 'Rp' . number_format((float) $amount, 0, ',', '.');
}

function normalize_phone(string $phone): string
{
    $phone = preg_replace('/[^0-9+]/', '', $phone) ?? '';
    if (str_starts_with($phone, '08')) {
        return '62' . substr($phone, 1);
    }
    if (str_starts_with($phone, '+62')) {
        return substr($phone, 1);
    }
    return $phone;
}

function active_programs(): array
{
    $stmt = db()->query('SELECT id, name, price, description FROM programs WHERE is_active = 1 ORDER BY sort_order ASC, name ASC');
    return $stmt->fetchAll();
}

function get_program(int $id): ?array
{
    $stmt = db()->prepare('SELECT id, name, price, description FROM programs WHERE id = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$id]);
    $program = $stmt->fetch();
    return $program ?: null;
}

function status_label(string $status): string
{
    return match ($status) {
        'new' => 'Baru',
        'waiting_payment' => 'Menunggu Pembayaran',
        'paid' => 'Sudah Bayar',
        'contacted' => 'Sudah Dihubungi',
        'trial' => 'Trial',
        'active' => 'Aktif',
        'cancelled' => 'Batal',
        'payment_failed' => 'Pembayaran Gagal',
        'payment_expired' => 'Pembayaran Kedaluwarsa',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
}

function payment_label(?string $status): string
{
    return match ($status) {
        'paid' => 'Lunas',
        'pending' => 'Pending',
        'challenge' => 'Challenge',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
        'cancelled' => 'Dibatalkan',
        'refunded' => 'Refund',
        default => $status ? ucfirst($status) : '-',
    };
}
