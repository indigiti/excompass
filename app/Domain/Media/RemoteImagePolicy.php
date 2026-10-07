<?php
declare(strict_types=1);

namespace ExCompass\Domain\Media;

final class RemoteImagePolicy
{
    /** @var string[] */
    private array $allowedHosts;

    public function __construct(?array $allowedHosts = null)
    {
        if ($allowedHosts === null) {
            $extra = array_filter(array_map('trim', explode(',', (string)(getenv('EXCOMPASS_IMAGE_HOSTS') ?: ''))));
            $allowedHosts = array_merge([
                'images.unsplash.com',
                'upload.wikimedia.org',
                'images.pexels.com',
            ], $extra);
        }
        $this->allowedHosts = array_values(array_unique(array_map('strtolower', $allowedHosts)));
    }

    public function sanitize(?string $url): ?string
    {
        $url = trim((string)$url);
        if ($url === '' || strlen($url) > 2048) {
            return null;
        }

        $parts = parse_url($url);
        if (!is_array($parts) || strtolower((string)($parts['scheme'] ?? '')) !== 'https') {
            return null;
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            return null;
        }

        $host = strtolower((string)($parts['host'] ?? ''));
        if ($host === '' || !$this->hostAllowed($host)) {
            return null;
        }

        return $url;
    }

    public function sanitizeMany(array $urls, int $limit = 8): array
    {
        $safe = [];
        foreach ($urls as $url) {
            $candidate = $this->sanitize(is_string($url) ? $url : null);
            if ($candidate !== null && !in_array($candidate, $safe, true)) {
                $safe[] = $candidate;
            }
            if (count($safe) >= $limit) {
                break;
            }
        }
        return $safe;
    }

    public function allowedHosts(): array
    {
        return $this->allowedHosts;
    }

    private function hostAllowed(string $host): bool
    {
        foreach ($this->allowedHosts as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
                return true;
            }
        }
        return false;
    }
}
