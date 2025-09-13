<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Post;
use App\Models\User;
use Core\Http\Response;

class ExampleController
{
    public function index(): Response
    {
        // // Your logic for the index method
        // $posts = Post::query()
        //     ->with('user')
        //     ->limit(5)
        //     ->fetch();

        // foreach ($posts as $post) {
        //     echo $post->title.PHP_EOL;
        //     echo $post->user['name'].PHP_EOL; // eager-loaded as array
        // }
        $user = User::query()
            ->with('posts')
            ->find(1);

        echo $user->name.PHP_EOL;

        foreach ($user->posts as $post) {
            echo $post->title.PHP_EOL;
        }

        return new Response()->setStatus(200);

    }

    public function about()
    {
        // Your logic for the about method
        return 'About Us';
    }
}
