<?php

declare(strict_types=1);

namespace Core\Http\Contracts;

interface ResponseInterface
{
    public function setStatus(int $code): static;
    public function setHeader(string $key, string $value): static;
    public function write(string $content): static;
    public function send(): void;
    public function json(array|object $data, int $status = 200): static;
    public function redirect(string $to, int $status = 302): static;
}
