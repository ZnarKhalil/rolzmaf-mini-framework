<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Http\Contracts\RequestInterface;

class Request implements RequestInterface
{
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
                $name           = strtolower(str_replace('_', '-', substr($k, 5)));
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
        $raw = file_get_contents('php://input');

        return json_decode($raw, true) ?? [];
    }

    public function all(): array
    {
        return array_merge($this->query(), $this->input(), $this->json());
    }
}
