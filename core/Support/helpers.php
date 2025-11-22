<?php

declare(strict_types=1);

use Core\Container\Container;

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('container')) {
    function container(): Container
    {
        return Container::getInstance();
    }
}

if (!function_exists('app')) {
    function app(?string $abstract = null): mixed
    {
        if ($abstract === null) {
            return container();
        }

        return container()->make($abstract);
    }
}
