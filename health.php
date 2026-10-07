<?php
declare(strict_types=1);

require __DIR__ . '/runtime.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$release = [];
$manifest = EXCOMPASS_PRIVATE_ROOT_PATH . '/build/release.json';
if (is_file($manifest)) {
    $decoded = json_decode((string) file_get_contents($manifest), true);
    if (is_array($decoded)) {
        $release = $decoded;
    }
}

echo json_encode([
    'ok' => true,
    'app' => 'ExCompass',
    'environment' => $config['environment'],
    'storage' => $storageConfig['driver'] ?? 'json',
    'sourceSha' => $release['sourceSha'] ?? null,
    'version' => $release['version'] ?? null,
], JSON_UNESCAPED_SLASHES);
