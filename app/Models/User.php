<?php

declare(strict_types=1);

namespace App\Models;

use Core\ORM\Model;
use Core\ORM\Relations\HasMany;

class User extends Model
{
    public static function posts(): HasMany
    {
        return new HasMany(new static(), Post::class, 'user_id');
    }
}
