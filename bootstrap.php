<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/Support/helpers.php';

use Dotenv\Dotenv;
use Core\Config\Config;
use Core\Logging\Logger;
use Core\Storage\DiskStorage;
use Core\Storage\StorageManager;
use Core\Support\ExceptionHandler;
use Core\Logging\Drivers\FileLogger;

// 1. Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// 2. Load config
Config::load(__DIR__ . '/config/app.php');

// 3. Set up logger
Logger::setDriver(new FileLogger(__DIR__ . '/storage/logs/app.log'));

// 4. Register global error/exception handler
ExceptionHandler::register();
StorageManager::setDriver(new DiskStorage(__DIR__ . '/storage/files'));