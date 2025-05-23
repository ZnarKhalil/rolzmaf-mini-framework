<?php
declare(strict_types=1);

namespace Core\Support;

use Core\Logging\Logger;
use Throwable;

class ExceptionHandler
{
    public static function register(): void
    {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError(int $severity, string $message, string $file, int $line): void
    {
        Logger::error("PHP Error: $message in $file on line $line");
    }

    public static function handleException(Throwable $e): void
    {
        Logger::error("Uncaught Exception: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}");
        http_response_code(500);
        echo "Internal Server Error";
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR])) {
            Logger::error("Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}");
        }
    }
}