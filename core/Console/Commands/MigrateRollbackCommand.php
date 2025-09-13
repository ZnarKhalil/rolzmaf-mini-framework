<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Console\CommandInterface;
use Core\Console\Input;
use Core\Database\DatabaseConfig;
use PDO;

class MigrateRollbackCommand implements CommandInterface
{
    public function name(): string
    {
        return 'migrate:rollback';
    }

    public function description(): string
    {
        return 'Rollback the last batch of migrations';
    }

    public function execute(Input $input): int
    {
        $pdo = DatabaseConfig::makePdo();

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $lastBatch = (int) $pdo->query('SELECT MAX(batch) FROM migrations')->fetchColumn();

        $stmt = $pdo->prepare('SELECT name FROM migrations WHERE batch = :batch ORDER BY id DESC');
        $stmt->execute(['batch' => $lastBatch]);

        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!$migrations) {
            echo "ℹ️  Nothing to rollback.\n";

            return 0;
        }

        foreach ($migrations as $name) {
            echo "⏪ Rolling back: $name\n";
            $migration = require __DIR__ . "/../../../database/migrations/$name";

            if (method_exists($migration, 'down')) {
                $migration->down();
            }

            $pdo->prepare('DELETE FROM migrations WHERE name = ?')->execute([$name]);
            echo "✅  Rolled back: $name\n";
        }

        return 0;
    }
}
