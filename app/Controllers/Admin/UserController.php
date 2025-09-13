<?php

/**
 * Rolzmaf — PHP mini framework
 * (c) 2025 Znar Khalil
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Http\Contracts\RequestInterface;
use Core\Http\Response;

class UserController
{
    public function index(RequestInterface $request): Response
    {
        return (new Response())->write('Hello from UserController@index');
    }
}
