<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Http\Contracts\RequestInterface;
use Core\Http\Response;

class ExampleController
{
    public function index(RequestInterface $request): Response
    {
        return new Response()->json(['message' => 'Hello from index']);
    }

    public function about(): Response
    {
        return new Response()->write('About page');
    }
}
