# SimpleAOP

SimpleAOP is a Laravel package that provides Aspect Oriented Programming (AOP) functionality for your Laravel applications. AOP is a programming paradigm that allows you to modularize cross-cutting concerns, such as logging, caching, transaction management etc. By defining aspects that can be applied to different controller class methods. With SimpleAOP, you can easily create and use aspects in your Laravel projects, using attributes classes which is PHP 8 feature.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Features](#features)
- [Requirements](#requirements)
- [Testing](#testing)
- [Issues and Features Requests](#issues-and-features-requests)
- [Contributors and License](#contributors-and-license)

## Installation

You can install SimpleAOP using Composer by running the following command:

```bash
composer require ahmadvoid/simple-aop
```

After installing the package, you need to register the `AspectServiceProvider` in your `config/app.php` file:

```php
'providers' => [
    // ...
    AhmadVoid\SimpleAOP\AspectServiceProvider::class,
];

You also need to passing your routes through `aspect` alias middleware in your `app/provider/RouteServiceProvider.php` file:

```php
public function boot()
{
    $this->configureRateLimiting();

    $this->routes(function () {
        Route::prefix('api')
            ->middleware('api')
            ->middleware('aspect') // passing requests in aspect
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));

        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
        });
}
```

Finally, you need to publish the package configuration file by running the following command:

```bash
php artisan vendor:publish --provider="AhmadVoid\SimpleAOP\AspectServiceProvider"
```

This will create a `config/aop.php` file where you can customize the settings of the package.

## Usage

To use SimpleAOP, you need to create aspects that define the logic that you want to apply to your methods or classes. You can create aspects using attributes classes which is PHP 8 feature that allow you to add metadata to your code using command:

```bash
php artisan make:aspect AspectName
```

This would create an aspect attribute class with this prototype in path app/Aspects
for Example:

```bash
php artisan make:aspect Logger
```

```php
<?php

namespace App\Aspects;

use AhmadVoid\SimpleAOP\Aspect;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Logger implements Aspect
{

    // The constructor can accept parameters for the attribute
    public function __construct()
    {

    }

    public function executeBefore($request, $controller, $method)
    {
        // TODO: Implement executeBefore() method.
    }

    public function executeAfter($request, $controller, $method, $response)
    {
        // TODO: Implement executeAfter() method.
    }

    public function executeException($request, $controller, $method, $exception)
    {
        // TODO: Implement executeException() method.
    }
}
```
### Aspect implementation

You can also create aspects using custom classes that extend the `Ahmadvoid\SimpleAOP\Aspect` abstract class. This class provides four abstract methods that you need to implement in your aspect class:

- `executeBefore`: This method is executed before the controller method is called. It receives the request, the controller instance, and the method name as parameters.

- `executeAfter`: This method is executed after the controller method is called. It receives the request, the controller instance, the method name, and the response as parameters.

- `executeException`: This method is executed if an exception is thrown during the controller method execution. It receives the request, the controller instance, the method name, and the exception as parameters.

let's write some implementation to log requests, response and onException occured:

```php
<?php

namespace App\Aspects;

use AhmadVoid\SimpleAOP\Aspect;
use Illuminate\Support\Facades\Log;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Logger implements Aspect
{
    // The constructor can accept parameters for the attribute
    public function __construct(public string $message = 'Logging...')
    {
        //
    }

    public function executeBefore($request, $controller, $method)
    {
        Log::info($this->message);
        Log::info('Request: ' . $request->fullUrl());
        Log::info('Controller: ' . get_class($controller));
        Log::info('Method: ' . $method);
    }

    public function executeAfter($request, $controller, $method, $response)
    {
        Log::info('Response: ' . $response->getContent());
    }

    public function executeException($request, $controller, $method, $exception)
    {
        Log::error($exception->getMessage());
    }
}
```


Then, you can apply the attribute to any controller method that you want to log:

```php
<?php

namespace App\Http\Controllers;

use App\Aspects\myAspect;
use Illuminate\Http\Request;

class TestController extends Controller
{
    #[Logger]
    public function index(Request $request)
    {
        var_dump('hello from index method');
    }
}
```

The route for index method for example:

```php
Route::get('test', [TestController::class, 'index']);
```

Now, whenever the `/test` route is accessed, the `Logger` aspect will be executed before and after the `index` method, and the request and response details will be logged.

We can also apply the aspects on all controller method by applying aspect attribute on controller class itself. for example:

```php
<?php

namespace App\Http\Controllers;

use App\Aspects\myAspect;
use Illuminate\Http\Request;

#[Logger]
class TestController extends Controller
{
    public function index(Request $request)
    {
        var_dump('hello from index method');
    }

    public function create(Request $request)
    {
        var_dump('hello from create method');
    }
}
```

This will lead to make Aspect 'Logger' Applied for each method in TestController.

You can create and use as many aspects as you want, and apply them to different methods or classes. You can also use multiple aspect attributes on the same method or class, and they will be executed in the order that they are defined.


## Features

SimpleAOP provides the following features for your Laravel applications:

- Easy creation and usage of aspects using attributes classes
- Support for 3 types of aspect methods: before, after and exception.
- Flexible configuration and customization of aspects.
- Caching of attribute instances for better performance
- Compatibility with PHP 8 and Laravel 8

## Requirements

SimpleAOP requires the following versions of PHP and Laravel:

- PHP >= 8.0
- Laravel >= 8.0

## Contributors and License

SimpleAOP is created and maintained by [Ahmad Alhalabi].

SimpleAOP is licensed under the [MIT License], which means you can use, modify, and distribute it freely, as long as you give credit to the original author and include the license file in your project.
