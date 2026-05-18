<?php
declare(strict_types=1);

$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    echo 'File konfigurasi belum ada. Copy app/config.example.php menjadi app/config.php lalu isi datanya.';
    exit;
}

$config = require $configFile;

date_default_timezone_set($config['timezone'] ?? 'Asia/Jakarta');

function app_config(?string $key = null, mixed $default = null): mixed
{
    global $config;
    if ($key === null) {
        return $config;
    }

    $segments = explode('.', $key);
    $value = $config;
    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

function app_url(string $path = ''): string
{
    $base = rtrim((string) app_config('base_url'), '/');
    $path = ltrim($path, '/');
    return $path === '' ? $base : $base . '/' . $path;
}
