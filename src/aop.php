<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Attribute Handler Properties
    |--------------------------------------------------------------------------
    |
    | This option controls the attribute handler caching enhance enabling
    | and specify key prefix and timeout storing method attributes
    |
    */

    'attribute_handler' => [
        'enable' => true,
        'key_prefix' => 'attribute', // Define the cache key prefix
        'cache_minutes' => 60, // Define the cache time in minutes
    ],
];
