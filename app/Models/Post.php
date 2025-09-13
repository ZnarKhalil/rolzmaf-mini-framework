<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace App\Models;

use Core\ORM\Relations\BelongsTo;

class Post extends \Core\ORM\Model
{
    public static function user(): BelongsTo
    {
        return new BelongsTo(new static(), User::class, 'user_id');
    }
}
