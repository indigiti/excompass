<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

use ExCompass\Support\Csrf;

if (!admin_needs_setup()) {
    if (admin_user()) {
        admin_redirect('');
    }
    admin_redirect('login.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::valid($_POST['_token'] ?? null)) {
        http_response_code(419);
        $error = 'Your session expired. Please try again.';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['password_confirm'] ?? '');

        try {
            if (mb_strlen($name) < 2) {
                throw new InvalidArgumentException('Enter your name.');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Enter a valid email address.');
            }
            if (strlen($password) < 12) {
                throw new InvalidArgumentException('Use at least 12 characters for the password.');
            }
            if ($password !== $confirm) {
                throw new InvalidArgumentException('Password confirmation does not match.');
            }

            $userRepository->createFirstAdmin(
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            );

            if (!$auth->attempt($email, $password)) {
                throw new RuntimeException('Administrator created, but automatic sign-in failed.');
            }

            admin_flash('Administrator account created.');
            admin_redirect('');
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Create first admin · ExCompass</title>
  <link rel="stylesheet" href="<?=e(u('assets/css/app.css'))?>">
  <link rel="stylesheet" href="<?=e(u('assets/css/admin.css'))?>">
</head>
<body class="admin-login-body">
<form class="admin-login admin-setup" method="post">
  <a class="brand" href="<?=e(u())?>"><span>Ex</span>Compass</a>
  <div class="setup-kicker">FIRST-RUN SETUP</div>
  <h1>Create first admin</h1>
  <p>No active administrator exists yet. Create the first account to unlock the ExCompass workspace.</p>
  <?php if($error):?><div class="admin-alert error"><?=e($error)?></div><?php endif;?>
  <input type="hidden" name="_token" value="<?=e(Csrf::token())?>">
  <label>Name<input type="text" name="name" required autocomplete="name" maxlength="120" value="<?=e((string)($_POST['name']??''))?>"></label>
  <label>Email<input type="email" name="email" required autocomplete="email" maxlength="190" value="<?=e((string)($_POST['email']??''))?>"></label>
  <label>Password<input type="password" name="password" required minlength="12" autocomplete="new-password"></label>
  <label>Confirm password<input type="password" name="password_confirm" required minlength="12" autocomplete="new-password"></label>
  <button type="submit">Create administrator</button>
  <small>This setup page automatically locks after the first active admin is created.</small>
</form>
</body>
</html>
