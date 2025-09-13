<?php

declare(strict_types=1);

return [
    'name'  => $_ENV['APP_NAME']  ?? 'Rolzmaf',
    'env'   => $_ENV['APP_ENV']   ?? 'local',
    'debug' => $_ENV['APP_DEBUG'] ?? true,
    'url'   => $_ENV['APP_URL']   ?? 'http://localhost',
];
