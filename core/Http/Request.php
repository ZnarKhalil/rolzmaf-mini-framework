<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Http\Contracts\RequestInterface;

class Request implements RequestInterface
{
    private ?string $jsonError = null;
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return strtok($uri, '?'); // strip query string
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? $_GET : ($_GET[$key] ?? $default);
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? $_POST : ($_POST[$key] ?? $default);
    }

    public function header(?string $key = null, mixed $default = null): mixed
    {
        $headers = [];

        foreach ($_SERVER as $k => $v) {
            if (str_starts_with($k, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($k, 5)));
                $headers[$name] = $v;
            }
        }

        if ($key === null) {
            return $headers;
        }

        return $headers[strtolower($key)] ?? $default;
    }

    public function json(): array
    {
        $this->jsonError = null;

        $contentType = (string) ($this->header('content-type') ?? '');
        if ($contentType === '' || stripos($contentType, 'application/json') === false) {
            return [];
        }

        $raw = file_get_contents('php://input') ?: '';
        if ($raw === '') {
            return [];
        }

        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->jsonError = json_last_error_msg();

            return [];
        }

        return $data ?? [];
    }

    public function all(): array
    {
        return array_merge($this->query(), $this->input(), $this->json());
    }

    public function jsonError(): ?string
    {
        return $this->jsonError;
    }

    public function string(string $key, string $default = ''): string
    {
        return (string) $this->input($key, $default);
    }

    public function int(string $key, int $default = 0): int
    {
        return (int) $this->input($key, $default);
    }

    public function bool(string $key, bool $default = false): bool
    {
        return filter_var($this->input($key, $default), FILTER_VALIDATE_BOOLEAN);
    }
}
