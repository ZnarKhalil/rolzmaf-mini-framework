<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Relations;

use PHPUnit\Framework\TestCase;
use Core\ORM\Relations\HasMany;
use Core\ORM\Model;
use PHPUnit\Framework\Attributes\CoversClass;
use PDO;
use PDOException;
use Tests\Unit\ORM\TestModel;

class HasManyUser extends TestModel {
    public static function table(): string { return 'users'; }
    public static function primaryKey(): string { return 'id'; }
}

class HasManyPost extends TestModel {
    public static function table(): string { return 'posts'; }
    public static function primaryKey(): string { return 'id'; }

    public static function allowedColumns(): array
    {
        return ['id', 'title', 'user_id'];
    }
}

#[CoversClass(HasMany::class)]
final class HasManyTest extends TestCase
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
            HasManyUser::setTestPdo($this->pdo);
            HasManyPost::setTestPdo($this->pdo);
        } catch (PDOException $e) {
            echo "Error creating tables: " . $e->getMessage() . "\n";
        }
    }
    
    #[Test]
    public function test_load(): void
    {
        $user = new HasManyUser(['id' => 1, 'name' => 'John']);
        $this->pdo->exec("INSERT INTO posts (user_id, title) VALUES (1, 'Post 1'), (1, 'Post 2')");
        $hasMany = new HasMany($user, HasManyPost::class, 'user_id');
        $loaded = $hasMany->load([$user]);
        $this->assertCount(2, $loaded[1]);
        $this->assertEquals('Post 1', $loaded[1][0]->title);
        $this->assertEquals('Post 2', $loaded[1][1]->title);
    }
} 