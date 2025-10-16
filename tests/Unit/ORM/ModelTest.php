<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Fixtures;

use Core\ORM\Model;

final class Category extends Model
{
}

final class Toy extends Model
{
}

final class Bus extends Model
{
}

final class Box extends Model
{
}

final class Church extends Model
{
}

final class Wolf extends Model
{
}

final class Knife extends Model
{
}

final class UserCategory extends Model
{
}

final class UserStatusCategory extends Model
{
}

namespace Tests\Unit\ORM;

use Core\ORM\Model;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ORM\Fixtures\Box;
use Tests\Unit\ORM\Fixtures\Bus;
use Tests\Unit\ORM\Fixtures\Category;
use Tests\Unit\ORM\Fixtures\Church;
use Tests\Unit\ORM\Fixtures\Knife;
use Tests\Unit\ORM\Fixtures\Toy;
use Tests\Unit\ORM\Fixtures\UserCategory;
use Tests\Unit\ORM\Fixtures\UserStatusCategory;
use Tests\Unit\ORM\Fixtures\Wolf;

#[CoversClass(Model::class)]
final class ModelTest extends TestCase
{
    #[Test]
    public function it_pluralizes_basic_cases(): void
    {
        $this->assertSame('categories', Category::table());
        $this->assertSame('toys', Toy::table());
        $this->assertSame('buses', Bus::table());
        $this->assertSame('boxes', Box::table());
        $this->assertSame('churches', Church::table());
        $this->assertSame('wolves', Wolf::table());
        $this->assertSame('knives', Knife::table());
    }

    #[Test]
    public function it_pluralizes_only_last_snake_segment(): void
    {
        $this->assertSame('user_categories', UserCategory::table());
        $this->assertSame('user_status_categories', UserStatusCategory::table());
    }

    #[Test]
    public function it_handles_attribute_access_and_array_conversion(): void
    {
        $model        = new Category();
        $model->name  = 'Books';
        $model->count = 3;

        $this->assertSame('Books', $model->name);
        $this->assertSame(3, $model->count);

        $array = $model->toArray();
        $this->assertSame('Books', $array['name'] ?? null);
        $this->assertSame(3, $array['count'] ?? null);
    }

    #[Test]
    public function it_has_default_primary_key_and_allowed_columns(): void
    {
        $this->assertSame('id', Category::primaryKey());

        $allowed = Category::allowedColumns();
        $this->assertContains('*', $allowed);
        $this->assertContains('id', $allowed);
        $this->assertContains('created_at', $allowed);
        $this->assertContains('updated_at', $allowed);
    }
}
