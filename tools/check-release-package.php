<?php
declare(strict_types=1);

$release = dirname(__DIR__) . '/release';

function must(bool $ok, string $message): void
{
    if (!$ok) {
        throw new RuntimeException($message);
    }
}

$required = [
    'RELEASE.json',
    'public/.htaccess',
    'public/runtime.php',
    'public/index.php',
    'public/rankings.php',
    'public/entity.php',
    'public/search.php',
    'public/methodology.php',
    'public/health.php',
    'public/assets/css/app.css',
    'public/assets/css/admin.css',
    'public/admin/index.php',
    'public/admin/login.php',
    'private/app/bootstrap.php',
    'private/config/app.php',
    'private/config/storage.php',
    'private/config/auth.php',
    'private/bin/create-admin.php',
    'private/database/migrations/001_core.sql',
    'private/build/release.json',
];

foreach ($required as $path) {
    must(is_file($release . '/' . $path), "payload missing: {$path}");
}

$meta = json_decode((string) file_get_contents($release . '/RELEASE.json'), true);
must(is_array($meta), 'invalid RELEASE.json');
must(($meta['schema'] ?? '') === 'DIGIOPS-RELEASE/1', 'release schema mismatch');
must(($meta['name'] ?? '') === 'ExCompass', 'release name mismatch');
must(($meta['version'] ?? '') === '1.0.0', 'release version mismatch');
must(($meta['publicPath'] ?? '') === 'public_html/excompass/', 'public path mismatch');
must(($meta['privatePath'] ?? '') === 'private_html/excompass/', 'private path mismatch');
must(($meta['persistentPaths'] ?? []) === ['storage/'], 'persistent storage contract mismatch');

$runtime = (string) file_get_contents($release . '/public/runtime.php');
foreach (['private_html/excompass', 'EXCOMPASS_PRIVATE_ROOT', 'EXCOMPASS_PRIVATE_ROOT_PATH'] as $needle) {
    must(str_contains($runtime, $needle), "runtime split contract missing: {$needle}");
}

$htaccess = (string) file_get_contents($release . '/public/.htaccess');
must(str_contains($htaccess, 'RewriteBase /excompass/'), 'RewriteBase /excompass/ missing');

$config = (string) file_get_contents($release . '/private/config/app.php');
must(str_contains($config, "defined('EXCOMPASS_BASE_PATH')"), 'deploy base-path fallback missing');

foreach (['index.php','rankings.php','entity.php','search.php','methodology.php','health.php'] as $page) {
    $content = (string) file_get_contents($release . '/public/' . $page);
    must(str_contains($content, "runtime.php"), "public runtime bridge missing: {$page}");
}

foreach ([
    'public/app',
    'public/config',
    'public/storage',
    'public/database',
    'public/.env',
    'private/.env',
] as $forbidden) {
    must(!file_exists($release . '/' . $forbidden), "forbidden release path: {$forbidden}");
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($release, FilesystemIterator::SKIP_DOTS)
);
foreach ($iterator as $file) {
    must(!$file->isLink(), 'symlink not allowed: ' . $file->getPathname());
    $name = $file->getFilename();
    must(!str_ends_with($name, '.json') || !str_contains($file->getPathname(), '/storage/'), 'runtime data must not ship: ' . $file->getPathname());
}

echo "ExCompass DigiOps release verification: PASS" . PHP_EOL;
