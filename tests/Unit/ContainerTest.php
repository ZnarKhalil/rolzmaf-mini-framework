<?php

declare(strict_types=1);

namespace Tests\Unit;

use Core\Container\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Container::class)]
class ContainerTest extends TestCase
{
    #[Test]
    public function it_can_resolve_class_without_dependencies(): void
    {
        $container = new Container();
        $instance = $container->make(SimpleClass::class);

        $this->assertInstanceOf(SimpleClass::class, $instance);
    }

    #[Test]
    public function it_can_resolve_class_with_dependencies(): void
    {
        $container = new Container();
        $instance = $container->make(ClassWithDependency::class);

        $this->assertInstanceOf(ClassWithDependency::class, $instance);
        $this->assertInstanceOf(SimpleClass::class, $instance->dependency);
    }

    #[Test]
    public function it_can_bind_interface_to_implementation(): void
    {
        $container = new Container();
        $container->bind(DependencyInterface::class, SimpleClass::class);

        $instance = $container->make(ClassWithInterfaceDependency::class);

        $this->assertInstanceOf(ClassWithInterfaceDependency::class, $instance);
        $this->assertInstanceOf(SimpleClass::class, $instance->dependency);
    }

    #[Test]
    public function it_singleton_returns_same_instance(): void
    {
        $container = new Container();
        $container->singleton(SimpleClass::class);

        $instance1 = $container->make(SimpleClass::class);
        $instance2 = $container->make(SimpleClass::class);

        $this->assertSame($instance1, $instance2);
    }
}

class SimpleClass implements DependencyInterface
{
}

interface DependencyInterface
{
}

class ClassWithDependency
{
    public function __construct(public SimpleClass $dependency)
    {
    }
}

class ClassWithInterfaceDependency
{
    public function __construct(public DependencyInterface $dependency)
    {
    }
}
