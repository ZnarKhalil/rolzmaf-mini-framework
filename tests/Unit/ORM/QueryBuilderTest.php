<?php


declare(strict_types=1);

namespace Tests\Unit\ORM;

use Core\ORM\QueryBuilder;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

class DummyModel extends TestModel
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

#[CoversClass(QueryBuilder::class)]
final class QueryBuilderTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('CREATE TABLE dummies (id INTEGER PRIMARY KEY, name TEXT, value INTEGER)');
        DummyModel::setTestPdo($this->pdo);
    }

    #[Test]
    public function test_insert_and_fetch(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'foo', 'value' => 42]);
        $results = $qb->fetch();
        $this->assertCount(1, $results);
        $this->assertEquals('foo', $results[0]->name);
        $this->assertEquals(42, $results[0]->value);
    }

    #[Test]
    public function test_where_and_order_by(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'a', 'value' => 1]);
        $qb->insert(['name' => 'b', 'value' => 2]);
        $results = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->where('value', '>', 1)
            ->orderBy('name', 'desc')
            ->fetch();
        $this->assertCount(1, $results);
        $this->assertEquals('b', $results[0]->name);
    }

    #[Test]
    public function test_where_id(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'x', 'value' => 10]);
        $qb->insert(['name' => 'y', 'value' => 20]);
        $results = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->whereIn('value', [10])
            ->fetch();
        $this->assertCount(1, $results);
        $this->assertEquals('x', $results[0]->name);
    }

    #[Test]
    public function test_limit(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        for ($i = 0; $i < 5; $i++) {
            $qb->insert(['name' => 'n'.$i, 'value' => $i]);
        }
        $results = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->limit(2)
            ->fetch();
        $this->assertCount(2, $results);
    }

    #[Test]
    public function test_update(): void
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
        $this->assertEquals('after', $results[0]->name);
    }

    #[Test]
    public function test_delete(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'delete_me', 'value' => 99]);

        $deleted = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->where('name', '=', 'delete_me')
            ->delete();

        $this->assertTrue($deleted);

        $results = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->fetch();
        $this->assertCount(0, $results);
    }

    #[Test]
    public function test_first_returns_single_model(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'first', 'value' => 1]);
        $qb->insert(['name' => 'second', 'value' => 2]);

        $model = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->orderBy('id')
            ->first();

        $this->assertInstanceOf(DummyModel::class, $model);
        $this->assertEquals('first', $model->name);
    }

    #[Test]
    public function test_exists_returns_true_when_match_found(): void
    {
        $qb = new QueryBuilder(new DummyModel(), $this->pdo);
        $qb->insert(['name' => 'check', 'value' => 10]);

        $exists = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->where('name', '=', 'check')
            ->exists();

        $this->assertTrue($exists);
    }

    #[Test]
    public function test_exists_returns_false_when_no_match(): void
    {
        $exists = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->where('name', '=', 'nope')
            ->exists();

        $this->assertFalse($exists);
    }

    #[Test]
    public function test_to_sql_outputs_correct_query(): void
    {
        $qb = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->select('id', 'name')
            ->where('value', '>', 1)
            ->orderBy('name')
            ->limit(5);

        $sqlInfo = $qb->toSql();

        $this->assertStringContainsString('SELECT id, name FROM', $sqlInfo['sql']);
        $this->assertStringContainsString('WHERE `value` > ?', $sqlInfo['sql']);
        $this->assertEquals([1], $sqlInfo['params']);
    }

    #[Test]
    public function test_invalid_column_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Disallowed column/');

        (new QueryBuilder(new DummyModel(), $this->pdo))
            ->select('DROP_TABLE');
    }

    #[Test]
    public function test_join_generates_valid_sql(): void
    {
        $qb = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->select('id')
            ->join('other_table', 'dummies.id', '=', 'other_table.dummy_id');

        $sql = $qb->toSql()['sql'];
        $this->assertStringContainsString('JOIN other_table ON dummies.id = other_table.dummy_id', $sql);
    }

    #[Test]
    public function test_left_join_generates_valid_sql(): void
    {
        $qb = (new QueryBuilder(new DummyModel(), $this->pdo))
            ->select('id')
            ->leftJoin('other_table', 'dummies.id', '=', 'other_table.dummy_id');

        $sql = $qb->toSql()['sql'];
        $this->assertStringContainsString('LEFT JOIN other_table ON dummies.id = other_table.dummy_id', $sql);
    }
}
