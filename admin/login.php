<?php
require_once __DIR__ . '/../app/auth.php';

if (current_admin()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (login_admin($email, $password)) {
        redirect('index.php');
    }
    $error = 'Email atau password salah.';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <main class="container tiny">
    <form class="card form" method="post">
      <h1>Login Admin</h1>
      <?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
      <?= csrf_field() ?>
      <label>Email
        <input type="email" name="email" required autofocus>
      </label>
      <label>Password
        <input type="password" name="password" required>
      </label>
      <button class="btn primary" type="submit">Masuk</button>
      <p class="muted small">Default awal ada di README. Segera ganti setelah instalasi.</p>
    </form>
  </main>
</body>
</html>
