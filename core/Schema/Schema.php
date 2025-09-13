<?php


declare(strict_types=1);

namespace Core\Schema;

use Core\Database\DatabaseConfig;
use Core\Schema\Grammars\GrammarInterface;
use Core\Schema\Grammars\MySqlGrammar;
use Core\Schema\Grammars\SqliteGrammar;
use PDO;

class Schema
{
    private static ?self $instance = null;
    private PDO $pdo;
    private GrammarInterface $grammar;

    public function __construct()
    {
        $this->pdo     = DatabaseConfig::makePdo();
        $this->grammar = match ($_ENV['DB_DRIVER'] ?? 'mysql') {
            'sqlite' => new SqliteGrammar(),
            default  => new MySqlGrammar(),
        };
    }

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }


    public function create(string $table, \Closure $callback): void
    {
        $def = new Table();
        $callback($def);

        $sql = $this->grammar->compileCreate($table, $def);
        $this->pdo->exec($sql);
    }


    public function dropIfExists(string $table): void
    {
        $sql = $this->grammar->compileDrop($table);
        $this->pdo->exec($sql);
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}
