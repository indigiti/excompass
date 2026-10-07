<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

use ExCompass\Support\Csrf;

if (admin_needs_setup()) {
    admin_redirect('setup.php');
}

if (admin_user()) {
    admin_redirect('');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attempts = array_values(array_filter((array) ($_SESSION['login_attempts'] ?? []), static fn($time): bool => (time() - (int) $time) < 60));
    if (count($attempts) >= 5) {
        $error = 'Too many attempts. Try again shortly.';
    } elseif (!Csrf::valid($_POST['_token'] ?? null)) {
        http_response_code(419);
        $error = 'Your session expired. Please try again.';
    } else {
        $attempts[] = time();
        $_SESSION['login_attempts'] = $attempts;
        if ($auth->attempt((string) ($_POST['email'] ?? ''), (string) ($_POST['password'] ?? ''))) {
            unset($_SESSION['login_attempts']);
            admin_redirect('');
        }
        $error = 'Invalid email or password.';
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin sign in · ExCompass</title><link rel="stylesheet" href="<?=e(u('assets/css/app.css'))?>"><link rel="stylesheet" href="<?=e(u('assets/css/admin.css'))?>"></head><body class="admin-login-body"><form class="admin-login" method="post"><a class="brand" href="<?=e(u())?>"><span>Ex</span>Compass</a><h1>Admin sign in</h1><p>Editorial, ranking and publishing workspace.</p><?php if($error):?><div class="admin-alert error"><?=e($error)?></div><?php endif;?><input type="hidden" name="_token" value="<?=e(Csrf::token())?>"><label>Email<input type="email" name="email" required autocomplete="username"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button type="submit">Sign in</button><small>Use your administrator account to access the editorial workspace.</small></form></body></html>
