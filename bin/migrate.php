<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/Infrastructure/Database/Connection.php';

use ExCompass\Infrastructure\Database\Connection;

$config = require dirname(__DIR__) . '/config/database.php';
$pdo = (new Connection($config))->pdo();

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(190) NOT NULL UNIQUE,
        applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$applied = $pdo->query('SELECT migration FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
$files = glob(dirname(__DIR__) . '/database/migrations/*.sql') ?: [];
sort($files);

foreach ($files as $file) {
    $name = basename($file);
    if (in_array($name, $applied, true)) {
        echo "SKIP  {$name}\n";
        continue;
    }

    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException("Cannot read {$name}");
    }

    $pdo->beginTransaction();
    try {
        $pdo->exec($sql);
        $statement = $pdo->prepare('INSERT INTO schema_migrations (migration) VALUES (?)');
        $statement->execute([$name]);
        $pdo->commit();
        echo "APPLY {$name}\n";
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
