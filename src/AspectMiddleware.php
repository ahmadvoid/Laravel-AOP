<?php

namespace AhmadVoid\SimpleAOP;

use Closure;
use Illuminate\Http\Request;

class AspectMiddleware
{
    private AspectHandler $aspectHandler;

    // Inject the AttributeHandler instance using dependency injection
    public function __construct(AspectHandler $aspectHandler)
    {
        $this->aspectHandler = $aspectHandler;
    }
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the controller class and method name from the request object
        $controller = $request->route()->getController();
        $action = $request->route()->getActionMethod();

        // If the controller and method are defined
        if ($controller && $action) {
            // Execute the aspects using the AspectHandler instance
            return $this->aspectHandler->executeAspects($request, $controller, $action, $next);
        }

        // Otherwise, proceed with the request and return the response
        return $next($request);
    }
}
