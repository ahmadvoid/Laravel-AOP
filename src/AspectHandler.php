<?php

namespace AhmadVoid\SimpleAOP;

use Closure;
use Illuminate\Http\Request;

class AspectHandler
{
    // Define a list of aspects to apply
    protected array $aspects = [];

    // Define an instance of the AttributeHandler class
    protected AttributeHandler $attributeHandler;

    // Inject the AttributeHandler instance using dependency injection
    public function __construct(AttributeHandler $attributeHandler)
    {
        $this->attributeHandler = $attributeHandler;
    }

    // Add an aspect to the list
    public function addAspects(array $aspects)
    {
        $this->aspects = $aspects;
    }

    // Execute the aspects for a given controller and method

    /**
     * @param \Illuminate\Http\Request $request
     * @param mixed $controller
     * @param string $method
     * @param Closure $next
     * @return mixed
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function executeAspects(Request $request, mixed $controller, string $method, Closure $next): mixed
    {
        // Read the attributes from the controller method using the AttributeHandler instance
        $attributes = $this->attributeHandler->readAttributes($controller, $method);

        // Add the attributes to the list of aspects
        $this->addAspects($attributes);

        try {
            // For each aspect in the list
            foreach ($this->aspects as $aspect) {
                // Execute the before method
                $aspect->executeBefore($request, $controller, $method);
            }

            // Try to proceed with the request and get the response
            $response = $next($request);
            $exception = $response->exception;

            // If an exception is thrown, execute the exception method for each aspect
            if ($exception)
                throw $exception;


            // Execute the after method for each aspect in reverse order
            foreach (array_reverse($this->aspects) as $aspect) {
                $aspect->executeAfter($request, $controller, $method, $response);
            }

        } catch (\Exception $exception) {
            foreach ($this->aspects as $aspect) {
                $aspect->executeException($request, $controller, $method, $exception);
            }
            // Rethrow the exception
            throw $exception;
        }

        // Return the response
        return $response;
    }
}
