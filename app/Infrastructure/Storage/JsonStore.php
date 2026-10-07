<?php
declare(strict_types=1);

namespace ExCompass\Infrastructure\Storage;

use RuntimeException;

final class JsonStore
{
    public function __construct(private readonly string $basePath)
    {
    }

    public function read(string $name, array $default = []): array
    {
        $path = $this->path($name);
        if (!is_file($path)) {
            return $default;
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw new RuntimeException('Unable to read JSON store.');
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new RuntimeException('Invalid JSON store.');
        }

        return $data;
    }

    public function write(string $name, array $data): void
    {
        $this->update($name, static fn(): array => $data, []);
    }

    public function update(string $name, callable $mutator, array $default = []): array
    {
        $this->ensureDirectory();
        $path = $this->path($name);
        $lockPath = $path . '.lock';
        $lock = fopen($lockPath, 'c+');

        if ($lock === false || !flock($lock, LOCK_EX)) {
            throw new RuntimeException('Unable to lock JSON store.');
        }

        try {
            $current = $default;
            if (is_file($path)) {
                $json = file_get_contents($path);
                $decoded = $json === false ? null : json_decode($json, true);
                if (!is_array($decoded)) {
                    throw new RuntimeException('Invalid JSON store.');
                }
                $current = $decoded;
            }

            $next = $mutator($current);
            if (!is_array($next)) {
                throw new RuntimeException('JSON store mutator must return an array.');
            }

            $encoded = json_encode($next, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($encoded === false) {
                throw new RuntimeException('Unable to encode JSON store.');
            }

            $temp = tempnam($this->basePath, 'excompass_');
            if ($temp === false || file_put_contents($temp, $encoded . PHP_EOL) === false) {
                throw new RuntimeException('Unable to write JSON store.');
            }

            if (!rename($temp, $path)) {
                @unlink($temp);
                throw new RuntimeException('Unable to replace JSON store.');
            }

            return $next;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function path(string $name): string
    {
        if (!preg_match('/^[a-z0-9._-]+$/i', $name)) {
            throw new RuntimeException('Invalid JSON store name.');
        }
        return rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
    }

    private function ensureDirectory(): void
    {
        if (!is_dir($this->basePath) && !mkdir($this->basePath, 0775, true) && !is_dir($this->basePath)) {
            throw new RuntimeException('Unable to create storage directory.');
        }
    }
}
