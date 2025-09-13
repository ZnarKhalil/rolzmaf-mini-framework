<?php

declare(strict_types=1);

namespace Core\Database;

use PDO;

class MigrationRunner
{
    private PDO $pdo;
    private string $migrationPath;

    public function __construct(PDO $pdo, string $migrationPath = __DIR__ . '/../../database/migrations')
    {
        $this->pdo           = $pdo;
        $this->migrationPath = $migrationPath;
        $this->createMigrationsTable();
    }

    public function run(): void
    {
        $files = glob($this->migrationPath . '/*.php');
        sort($files);

        $applied = $this->getAppliedMigrations();
        $batch   = $this->getNextBatchNumber();

        foreach ($files as $file) {
            $name = basename($file);

            if (in_array($name, $applied, true)) {
                continue;
            }

            echo "🔁 Running migration: {$name}\n";

            $migration = require $file;

            if (!method_exists($migration, 'up')) {
                echo "⚠️  Skipped: no up() method in $name\n";
                continue;
            }

            $this->pdo->beginTransaction();

            try {
                $migration->up();
                $stmt = $this->pdo->prepare('INSERT INTO migrations (name, batch) VALUES (:name, :batch)');
                $stmt->execute(['name' => $name, 'batch' => $batch]);
                $this->pdo->commit();

                echo "✅  Migrated: $name\n";
            } catch (\Throwable $e) {
                $this->pdo->rollBack();
                echo "❌  Failed: $name\n";
                echo $e->getMessage() . "\n";
                exit(1);
            }
        }

        echo "🎉  Migrations complete.\n";
    }

    private function createMigrationsTable(): void
    {
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }

    private function getAppliedMigrations(): array
    {
        $stmt = $this->pdo->query('SELECT name FROM migrations');

        return $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
    }

    private function getNextBatchNumber(): int
    {
        $stmt = $this->pdo->query('SELECT MAX(batch) FROM migrations');

        return ((int) $stmt->fetchColumn()) + 1;
    }
}
