<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Relations\Fixtures;

use Tests\Unit\ORM\TestModel;

final class HasManyUser extends TestModel
{
    public static function table(): string
    {
        return 'users';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }
}

final class HasManyPost extends TestModel
{
    public static function table(): string
    {
        return 'posts';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function allowedColumns(): array
    {
        return ['id', 'title', 'user_id'];
    }
}

namespace Tests\Unit\ORM\Relations;

use Core\ORM\Relations\HasMany;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ORM\Relations\Fixtures\HasManyPost;
use Tests\Unit\ORM\Relations\Fixtures\HasManyUser;

#[CoversClass(HasMany::class)]
final class HasManyTest extends TestCase
{
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');
        $this->pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY, user_id INTEGER, title TEXT)');

        HasManyUser::setTestPdo($this->pdo);
        HasManyPost::setTestPdo($this->pdo);
    }

    protected function tearDown(): void
    {
        HasManyUser::clearTestPdo();
        HasManyPost::clearTestPdo();
        $this->pdo = null;

        parent::tearDown();
    }

    #[Test]
    public function it_loads_related_collections(): void
    {
        $user = new HasManyUser(['id' => 1, 'name' => 'John']);
        $this->pdo->exec("INSERT INTO posts (user_id, title) VALUES (1, 'Post 1'), (1, 'Post 2')");

        $hasMany = new HasMany($user, HasManyPost::class, 'user_id');
        $loaded  = $hasMany->load([$user]);

        $this->assertCount(2, $loaded[1]);
        $this->assertSame('Post 1', $loaded[1][0]->title);
        $this->assertSame('Post 2', $loaded[1][1]->title);
    }
}
