<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';
require __DIR__ . '/_layout.php';
require_once EXCOMPASS_PRIVATE_ROOT_PATH . '/app/Infrastructure/Storage/JsonAuditLog.php';

use ExCompass\Infrastructure\Storage\JsonAuditLog;
use ExCompass\Support\Csrf;

$user = require_admin();
$audit = new JsonAuditLog($store);
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::valid($_POST['_token'] ?? null)) {
        http_response_code(419);
        $error = 'Your session expired. Please try again.';
    } else {
        $current = (string)($_POST['current_password'] ?? '');
        $next = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['new_password_confirm'] ?? '');

        try {
            if (!password_verify($current, (string)($user['password_hash'] ?? ''))) {
                throw new InvalidArgumentException('Current password is incorrect.');
            }
            if (strlen($next) < 12) {
                throw new InvalidArgumentException('Use at least 12 characters for the new password.');
            }
            if ($next !== $confirm) {
                throw new InvalidArgumentException('New password confirmation does not match.');
            }
            if (password_verify($next, (string)($user['password_hash'] ?? ''))) {
                throw new InvalidArgumentException('Choose a different password from your current password.');
            }

            $userRepository->updatePassword(
                (int)$user['id'],
                password_hash($next, PASSWORD_DEFAULT)
            );
            $auth->rotateSession();
            $audit->record(
                $user,
                'user.password_changed',
                'user',
                (string)($user['email'] ?? $user['id']),
                null,
                null
            );
            admin_flash('Password changed successfully.');
            admin_redirect('account.php');
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}

admin_header('Account & security', $user);
?>
<div class="admin-page-head">
  <div><span>ACCOUNT</span><h1>Account &amp; security</h1></div>
</div>

<?php if($flash=admin_flash()):?><div class="admin-alert success"><?=e($flash)?></div><?php endif;?>
<?php if($error):?><div class="admin-alert error"><?=e($error)?></div><?php endif;?>

<div class="admin-grid account-grid">
  <section class="admin-panel">
    <div class="admin-panel-head"><h2>Your account</h2></div>
    <dl class="account-details">
      <div><dt>Name</dt><dd><?=e((string)$user['name'])?></dd></div>
      <div><dt>Email</dt><dd><?=e((string)$user['email'])?></dd></div>
      <div><dt>Role</dt><dd><?=e(implode(', ', array_map('ucfirst', (array)($user['roles']??[]))))?></dd></div>
      <div><dt>Status</dt><dd><?=e(ucfirst((string)($user['status']??'active')))?></dd></div>
    </dl>
  </section>

  <section class="admin-panel">
    <div class="admin-panel-head"><h2>Change password</h2></div>
    <p class="admin-muted">Confirm your current password, then choose a new password with at least 12 characters.</p>
    <form class="admin-form password-form" method="post">
      <input type="hidden" name="_token" value="<?=e(Csrf::token())?>">
      <label>Current password<input type="password" name="current_password" required autocomplete="current-password"></label>
      <label>New password<input type="password" name="new_password" required minlength="12" autocomplete="new-password"></label>
      <label>Confirm new password<input type="password" name="new_password_confirm" required minlength="12" autocomplete="new-password"></label>
      <button class="admin-primary" type="submit">Change password</button>
    </form>
  </section>
</div>
<?php admin_footer(); ?>
