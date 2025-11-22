<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\QueryBuilder\Fixtures;

use Tests\Unit\ORM\TestModel;

final class DummyModel extends TestModel
{
    public static function table(): string
    {
        return 'dummies';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function allowedColumns(): array
    {
        return ['id', 'name', 'value'];
    }
}

namespace Tests\Unit\ORM;

use Core\ORM\QueryBuilder;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ORM\QueryBuilder\Fixtures\DummyModel;

#[CoversClass(QueryBuilder::class)]
final class QueryBuilderTest extends TestCase
{
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('CREATE TABLE dummies (id INTEGER PRIMARY KEY, name TEXT, value INTEGER)');

        DummyModel::setTestPdo($this->pdo);
    }

    protected function tearDown(): void
    {
        DummyModel::clearTestPdo();
        $this->pdo = null;

        parent::tearDown();
    }

    #[Test]
    public function it_inserts_and_fetches_records(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'foo', 'value' => 42]);

        $results = $qb->fetch();

        $this->assertCount(1, $results);
        $this->assertSame('foo', $results[0]->name);
        $this->assertSame(42, $results[0]->value);
    }

    #[Test]
    public function it_filters_and_sorts(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'a', 'value' => 1]);
        $qb->insert(['name' => 'b', 'value' => 2]);

        $results = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->where('value', '>', 1)
            ->orderBy('name', 'desc')
            ->fetch();

        $this->assertCount(1, $results);
        $this->assertSame('b', $results[0]->name);
    }

    #[Test]
    public function it_supports_where_in(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'x', 'value' => 10]);
        $qb->insert(['name' => 'y', 'value' => 20]);

        $results = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->whereIn('value', [10])
            ->fetch();

        $this->assertCount(1, $results);
        $this->assertSame('x', $results[0]->name);
    }

    #[Test]
    public function it_limits_results(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        for ($i = 0; $i < 5; $i++) {
            $qb->insert(['name' => "n{$i}", 'value' => $i]);
        }

        $results = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->limit(2)
            ->fetch();

        $this->assertCount(2, $results);
    }

    #[Test]
    public function it_updates_records(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'before', 'value' => 1]);

        $updated = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->where('name', '=', 'before')
            ->update(['name' => 'after']);

        $this->assertTrue($updated);

        $results = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->where('name', '=', 'after')
            ->fetch();

        $this->assertCount(1, $results);
        $this->assertSame('after', $results[0]->name);
    }

    #[Test]
    public function it_deletes_records(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'delete_me', 'value' => 99]);

        $deleted = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->where('name', '=', 'delete_me')
            ->delete();

        $this->assertTrue($deleted);

        $results = (new QueryBuilder(new DummyModel(), $this->pdo))->fetch();
        $this->assertCount(0, $results);
    }

    #[Test]
    public function it_returns_first_record(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'first', 'value' => 1]);
        $qb->insert(['name' => 'second', 'value' => 2]);

        $model = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->orderBy('id')
            ->first();

        $this->assertInstanceOf(DummyModel::class, $model);
        $this->assertSame('first', $model->name);
    }

    #[Test]
    public function it_checks_for_existence(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'check', 'value' => 10]);

        $exists = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->where('name', '=', 'check')
            ->exists();

        $this->assertTrue($exists);
    }

    #[Test]
    public function it_detects_missing_records(): void
    {
        $exists = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->where('name', '=', 'nope')
            ->exists();

        $this->assertFalse($exists);
    }

    #[Test]
    public function it_outputs_sql_with_parameters(): void
    {
        $qb = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->select('id', 'name')
            ->where('value', '>', 1)
            ->orderBy('name')
            ->limit(5);

        $sqlInfo = $qb->toSql();

        $this->assertStringContainsString('SELECT id, name FROM', $sqlInfo['sql']);
        $this->assertStringContainsString('WHERE `value` > ?', $sqlInfo['sql']);
        $this->assertSame([1], $sqlInfo['params']);
    }

    #[Test]
    public function it_rejects_invalid_columns(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Disallowed column/');

        (new QueryBuilder(new DummyModel(), $this->pdo))->select('DROP_TABLE');
    }

    #[Test]
    public function it_builds_join_queries(): void
    {
        $qb = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->select('id')
            ->join('other_table', 'dummies.id', '=', 'other_table.dummy_id');

        $sql = $qb->toSql()['sql'];

        $this->assertStringContainsString(
            'JOIN other_table ON dummies.id = other_table.dummy_id',
            $sql
        );
    }

    #[Test]
    public function it_builds_left_join_queries(): void
    {
        $qb = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->select('id')
            ->leftJoin('other_table', 'dummies.id', '=', 'other_table.dummy_id');

        $sql = $qb->toSql()['sql'];

        $this->assertStringContainsString(
            'LEFT JOIN other_table ON dummies.id = other_table.dummy_id',
            $sql
        );
    }
}
