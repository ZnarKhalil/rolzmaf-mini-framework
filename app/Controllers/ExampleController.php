<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Post;
use App\Models\User;
use Core\Http\Response;

class ExampleController
{
    public function index(): Response
    {
        $user = User::query()
            ->with('posts')
            ->find(1);

        echo $user->name.PHP_EOL;

        foreach ($user->posts as $post) {
            echo $post->title.PHP_EOL;
        }

        return new Response()->setStatus(200);

    }

    public function about(): Response
    {
        return new Response()->setStatus(200)->write("About page");
    }
}
