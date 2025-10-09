<?php

declare(strict_types=1);

namespace Tests\Unit\ORM;

use Core\ORM\Model;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

// Test fixtures with various pluralization endings
class Category extends Model
{
}
class Toy extends Model
{
}
class Bus extends Model
{
}
class Box extends Model
{
}
class Church extends Model
{
}
class Wolf extends Model
{
}
class Knife extends Model
{
}
class UserCategory extends Model
{
}
class UserStatusCategory extends Model
{
}

#[CoversClass(Model::class)]
final class ModelTest extends TestCase
{
    public function test_table_pluralization_basic_cases(): void
    {
        $this->assertSame('categories', Category::table());
        $this->assertSame('toys', Toy::table());
        $this->assertSame('buses', Bus::table());
        $this->assertSame('boxes', Box::table());
        $this->assertSame('churches', Church::table());
        $this->assertSame('wolves', Wolf::table());
        $this->assertSame('knives', Knife::table());
    }

    public function test_table_pluralizes_only_last_snake_segment(): void
    {
        $this->assertSame('user_categories', UserCategory::table());
        $this->assertSame('user_status_categories', UserStatusCategory::table());
    }

    public function test_attribute_access_and_to_array(): void
    {
        $m        = new Category();
        $m->name  = 'Books';
        $m->count = 3;

        $this->assertSame('Books', $m->name);
        $this->assertSame(3, $m->count);

        $arr = $m->toArray();
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayHasKey('count', $arr);
        $this->assertSame('Books', $arr['name']);
        $this->assertSame(3, $arr['count']);
    }

    public function test_default_primary_key_and_allowed_columns(): void
    {
        $this->assertSame('id', Category::primaryKey());

        $allowed = Category::allowedColumns();
        $this->assertIsArray($allowed);
        $this->assertContains('*', $allowed);
        $this->assertContains('id', $allowed);
        $this->assertContains('created_at', $allowed);
        $this->assertContains('updated_at', $allowed);
    }
}
