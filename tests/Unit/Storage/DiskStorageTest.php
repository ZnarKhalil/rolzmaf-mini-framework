<?php

declare(strict_types=1);

namespace Tests\Unit\Storage;

use Core\Storage\DiskStorage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DiskStorage::class)]
final class DiskStorageTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        $this->root = sys_get_temp_dir() . '/rolzmaf_storage_' . bin2hex(random_bytes(4));
        mkdir($this->root, 0777, true);
    }

    #[Test]
    public function it_can_put_and_get_file(): void
    {
        $storage = new DiskStorage($this->root);
        $path    = 'test/file.txt';
        $content = 'Hello world';

        $storage->put($path, $content);
        $this->assertTrue($storage->exists($path));
        $this->assertSame($content, $storage->get($path));
    }

    #[Test]
    public function it_can_delete_file(): void
    {
        $storage = new DiskStorage($this->root);
        $path    = 'delete/me.txt';

        $storage->put($path, 'delete me');
        $this->assertTrue($storage->exists($path));

        $storage->delete($path);
        $this->assertFalse($storage->exists($path));
    }

    #[Test]
    public function it_returns_null_if_file_not_exists(): void
    {
        $storage = new DiskStorage($this->root);
        $this->assertNull($storage->get('missing.txt'));
    }

    #[Test]
    public function it_generates_url(): void
    {
        $storage = new DiskStorage($this->root);
        $this->assertSame('/storage/avatar.jpg', $storage->url('avatar.jpg'));
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->root);
        parent::tearDown();
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (scandir($dir) ?: [] as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = "$dir/$file";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
