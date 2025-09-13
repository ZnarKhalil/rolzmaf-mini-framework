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
}
