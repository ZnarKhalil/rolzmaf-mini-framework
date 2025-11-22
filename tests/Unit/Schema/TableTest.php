<?php

declare(strict_types=1);

namespace Tests\Unit\Schema;

use Core\Schema\Table;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Table::class)]
final class TableTest extends TestCase
{
    #[Test]
    public function it_defines_basic_columns(): void
    {
        $table = new Table();
        $table->id();
        $table->string('title');
        $table->timestamps();

        $this->assertCount(4, $table->columns);
        $this->assertSame('title', $table->columns[1]->name);
        $this->assertSame('created_at', $table->columns[2]->name);
    }

    #[Test]
    public function it_defines_indexes(): void
    {
        $table = new Table();
        $table->index(['user_id', 'status']);

        $this->assertNotEmpty($table->indexes);
        $this->assertStringContainsString('user_id', $table->indexes[0]);
    }
}
