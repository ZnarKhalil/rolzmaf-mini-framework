<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Relations\Fixtures;

use Tests\Unit\ORM\TestModel;

final class BelongsToUser extends TestModel
{
    public static function table(): string
    {
        return 'users';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function allowedColumns(): array
    {
        return ['id', 'name'];
    }
}

final class BelongsToPost extends TestModel
{
    public static function table(): string
    {
        return 'posts';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }
}

namespace Tests\Unit\ORM\Relations;

use Core\ORM\Relations\BelongsTo;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ORM\Relations\Fixtures\BelongsToPost;
use Tests\Unit\ORM\Relations\Fixtures\BelongsToUser;

#[CoversClass(BelongsTo::class)]
final class BelongsToTest extends TestCase
{
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');
        $this->pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY, user_id INTEGER, title TEXT)');

        BelongsToUser::setTestPdo($this->pdo);
        BelongsToPost::setTestPdo($this->pdo);
    }

    protected function tearDown(): void
    {
        BelongsToUser::clearTestPdo();
        BelongsToPost::clearTestPdo();
        $this->pdo = null;

        parent::tearDown();
    }

    #[Test]
    public function it_loads_related_models(): void
    {
        $this->pdo->exec("INSERT INTO users (id, name) VALUES (1, 'John')");

        $post      = new BelongsToPost(['id' => 1, 'user_id' => 1, 'title' => 'Post 1']);
        $belongsTo = new BelongsTo($post, BelongsToUser::class, 'user_id');

        $loaded = $belongsTo->load([$post]);

        $this->assertCount(1, $loaded);
        $this->assertSame('John', $loaded[1]->name);
    }
}
