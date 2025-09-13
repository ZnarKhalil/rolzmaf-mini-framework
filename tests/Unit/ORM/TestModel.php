<?php

declare(strict_types=1);

namespace Tests\Unit\ORM;

use Core\ORM\Model;
use PDO;

class TestModel extends Model
{
    private static ?PDO $testPdo = null;

    public static function setTestPdo(PDO $pdo): void
    {
        self::$testPdo = $pdo;
    }

    public static function query(): \Core\ORM\QueryBuilder
    {
        if (self::$testPdo === null) {
            throw new \RuntimeException('Test PDO instance not set. Call TestModel::setTestPdo() first.');
        }

        return new \Core\ORM\QueryBuilder(new static(), self::$testPdo);
    }
}
