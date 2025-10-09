<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Http\Contracts\ResponseInterface;

class Response implements ResponseInterface
{
    protected int $status     = 200;
    protected array $headers  = [];
    protected string $content = '';

    public function setStatus(int $code): static
    {
        $this->status = $code;

        return $this;
    }

    public function setHeader(string $key, string $value): static
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function write(string $content): static
    {
        $this->content .= $content;

        return $this;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        echo $this->content;
    }

    public function json(array|object $data, int $status = 200): static
    {
        $this->setStatus($status);
        $this->setHeader('Content-Type', 'application/json; charset=utf-8');
        $this->content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $this;
    }

    public function redirect(string $to, int $status = 302): static
    {
        $this->setStatus($status);
        $this->setHeader('Location', $to);

        return $this;
    }
}
