<?php
/**
 * Copy file ini menjadi config.php lalu isi sesuai data hosting dan Midtrans kamu.
 * Jangan share Server Key Midtrans ke publik.
 */
return [
    'app_name' => 'Ellaifa Quran - Pendaftaran',
    'base_url' => 'https://ellaifaquran.my.id/pendaftaran',
    'timezone' => 'Asia/Jakarta',

    'database' => [
        'host' => 'localhost',
        'name' => 'ellaifa_pendaftaran',
        'user' => 'ellaifa_user',
        'pass' => 'GANTI_PASSWORD_DATABASE',
        'charset' => 'utf8mb4',
    ],

    'midtrans' => [
        // Sandbox: false. Production/live: true.
        'is_production' => false,

        // Ambil dari Midtrans Dashboard > Settings > Access Keys.
        'server_key' => 'SB-Mid-server-ISI_SERVER_KEY_SANDBOX',
        'client_key' => 'SB-Mid-client-ISI_CLIENT_KEY_SANDBOX',

        // Disarankan diisi sama seperti URL publik file midtrans_notification.php.
        'notification_url' => 'https://ellaifaquran.my.id/pendaftaran/midtrans_notification.php',
    ],

    // Nilai ini akan dipakai ketika program belum punya nama cabang spesifik.
    'registration' => [
        'default_branch' => 'Online',
    ],
];
