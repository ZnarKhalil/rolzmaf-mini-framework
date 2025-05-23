<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Relations;

use PHPUnit\Framework\TestCase;
use Core\ORM\Relations\BelongsTo;
use Core\ORM\Model;
use PHPUnit\Framework\Attributes\CoversClass;
use PDO;
use PDOException;
use Tests\Unit\ORM\TestModel;

class BelongsToUser extends TestModel {
    public static function table(): string { return 'users'; }
    public static function primaryKey(): string { return 'id'; }

    public static function allowedColumns(): array
    {
        return ['id', 'name'];
    }
}

class BelongsToPost extends TestModel {
    public static function table(): string { return 'posts'; }
    public static function primaryKey(): string { return 'id'; }
}

#[CoversClass(BelongsTo::class)]
final class BelongsToTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $this->pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');
            $this->pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY, user_id INTEGER, title TEXT)');
            
            // Set the test PDO instance for both models
            BelongsToUser::setTestPdo($this->pdo);
            BelongsToPost::setTestPdo($this->pdo);
        } catch (PDOException $e) {
            echo "Error creating tables: " . $e->getMessage() . "\n";
        }
    }

    #[Test]
    public function test_load(): void
    {
        $this->pdo->exec("INSERT INTO users (id, name) VALUES (1, 'John')");
        $post = new BelongsToPost(['id' => 1, 'user_id' => 1, 'title' => 'Post 1']);
        $belongsTo = new BelongsTo($post, BelongsToUser::class, 'user_id');
        $loaded = $belongsTo->load([$post]);
        $this->assertCount(1, $loaded);
        $this->assertEquals('John', $loaded[1]->name);
    }
} 