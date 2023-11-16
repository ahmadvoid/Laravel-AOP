<?php

namespace AhmadVoid\SimpleAOP;

use Illuminate\Support\Facades\Cache;
use ReflectionClass;

class AttributeHandler
{
    // Implement the method for reading the attributes from the controller method
    /**
     * @throws \ReflectionException
     */
    public function readAttributes($controller, $method): array
    {
        $cachingEnable = config('aop.attribute_handler.enable', false);

        if ($cachingEnable === true) {
            // Generate a cache key based on the controller and method names
            $cacheKey = config('aop.attribute_handler.key_prefix') . ':' . get_class($controller) . ':' . $method;

            // Return the cached value or store the attribute instances in the cache
            return Cache::remember($cacheKey, config('aop.attribute_handler.cache_minutes'), function () use ($controller, $method) {
                // Get the attribute instances
                return $this->getAttributes($controller, $method);
            });
        }

        // If caching is disabled, return the attribute instances without caching
        return $this->getAttributes($controller, $method);
    }

    // Define the method that gets the attribute instances
    /**
     * @throws \ReflectionException
     */
    protected function getAttributes($controller, $method): array
    {
        // Create a reflection class for the controller
        $reflection = new ReflectionClass($controller);

        // Get the reflection method for the method name
        $reflectionMethod = $reflection->getMethod($method);

        // Get the attributes that have the #[\Attribute(\Attribute::TARGET_METHOD)] attribute
        $attributes = collect($reflection->getAttributes())->merge($reflectionMethod->getAttributes());

        // Get the attribute instances
        $attributes = $attributes->map(function ($attribute) {
            return $attribute->newInstance();
        });

        // Filter the attribute instances by the 'Aspect' interface
        $attributes = $attributes->filter(function ($attribute) {
            return $attribute instanceof Aspect;
        });

        // Return the filtered attributes as an array
        return $attributes->toArray();
    }
}
