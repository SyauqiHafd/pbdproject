<?php
require_once __DIR__ . '/bootstrap.php';

$auth = new AuthController();
if ($user = $auth->user()) {
    header('Location: ' . AuthController::homeFor($user));
    exit;
}

$error    = null;
$username = '';
$info     = isset($_GET['out']) ? 'Anda telah logout.' : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Session::checkCsrf($_POST['csrf'] ?? null)) {
        $error = 'Sesi kedaluwarsa, silakan coba lagi.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $result   = $auth->login($username, (string) ($_POST['password'] ?? ''));
        if ($result['ok']) {
            header('Location: ' . AuthController::homeFor($auth->user()));
            exit;
        }
        $error = $result['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Log In</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="login-body">
  <div class="login-card">
    <div class="login-brand"><span class="brand-mark">J</span></div>
    <div class="login-head">
      <h3>PT. Jaya Group Pengadaan</h3>
      <p>Login to continue</p>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= Html::e($error) ?></div><?php endif; ?>
    <?php if ($info && !$error): ?><div class="alert alert-info"><?= Html::e($info) ?></div><?php endif; ?>

    <form method="post" action="login.php" autocomplete="off">
      <input type="hidden" name="csrf" value="<?= Html::e(Session::csrfToken()) ?>">
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username" value="<?= Html::e($username) ?>" required autofocus>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn btn-dark btn-block">Log In</button>
    </form>
  </div>
</body>
</html>
