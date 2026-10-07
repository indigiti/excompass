<?php
declare(strict_types=1);

function admin_header(string $title, array $user): void
{
    ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> · ExCompass Admin</title><link rel="stylesheet" href="<?=e(u('assets/css/app.css'))?>"><link rel="stylesheet" href="<?=e(u('assets/css/admin.css'))?>"></head><body class="admin-body">
    <header class="admin-top"><div class="admin-shell"><a class="brand" href="<?=e(u('admin/'))?>"><span>Ex</span>Compass <small>Admin</small></a><nav><a href="<?=e(u('admin/'))?>">Dashboard</a><a href="<?=e(u('admin/entities.php'))?>">Entities</a><a href="<?=e(u())?>" target="_blank" rel="noopener">View site ↗</a></nav><div class="admin-user"><span><?=e($user['name'])?></span><form method="post" action="<?=e(u('admin/logout.php'))?>"><input type="hidden" name="_token" value="<?=e(ExCompassSupportCsrf::token())?>"><button>Sign out</button></form></div></div></header><main class="admin-shell admin-main"><?php
}

function admin_footer(): void
{
    ?></main></body></html><?php
}
