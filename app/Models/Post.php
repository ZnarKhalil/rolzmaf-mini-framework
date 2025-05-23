<?php

namespace App\Models;

use App\Models\User;
use Core\ORM\Relations\BelongsTo;

class Post extends \Core\ORM\Model
{
    public static function user(): BelongsTo
    {
        return new BelongsTo(new static(), User::class, 'user_id');
    }
}