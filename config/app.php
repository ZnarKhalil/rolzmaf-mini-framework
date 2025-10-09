<?php

declare(strict_types=1);

return [
    'name'   => $_ENV['APP_NAME']  ?? 'Rolzmaf',
    'env'    => $_ENV['APP_ENV']   ?? 'local',
    'debug'  => $_ENV['APP_DEBUG'] ?? true,
    'url'    => $_ENV['APP_URL']   ?? 'http://localhost',
    'cookie' => [
        // Defaults can be overridden via this config array
        // When not set, secure defaults are derived from env/url
        // 'secure'   => true,
        // 'httponly' => true,
        // 'samesite' => 'Lax', // Lax | Strict | None
        // 'path'     => '/',
    ],
];
