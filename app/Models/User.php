<?php

namespace App\Models;

use Core\ORM\Relations\HasMany;

class User extends \Core\ORM\Model
{
    public static function posts(): HasMany
    {
        return new HasMany(new static(), Post::class, 'user_id');
    }
}