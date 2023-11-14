<?php

namespace AhmadVoid\SimpleAOP;

use Illuminate\Http\Request;

interface Aspect
{
    // Define the methods for executing the aspects
    public function executeBefore(Request $request, mixed $controller, string $method);

    public function executeAfter(Request $request, mixed $controller, string $method, mixed $response);

    public function executeException(Request $request, mixed $controller, string $method, \Exception $exception);
}
