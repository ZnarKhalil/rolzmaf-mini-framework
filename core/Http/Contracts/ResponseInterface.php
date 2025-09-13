<?php


declare(strict_types=1);

namespace Core\Http\Contracts;

interface ResponseInterface
{
    public function setStatus(int $code): static;
    public function setHeader(string $key, string $value): static;
    public function write(string $content): static;
    public function send(): void;
}
