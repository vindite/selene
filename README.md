[![MIT License](https://img.shields.io/apm/l/atomic-design-ui.svg?)](https://github.com/ovalves/selene/blob/master/LICENSE)
[![Read the Docs](https://readthedocs.org/projects/selene-framework/badge/?version=latest)](https://selene-framework.readthedocs.io/en/latest/?badge=latest)
[![PR's Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat)](http://makeapullrequest.com)

## About Selene Microframework

Selene is a PHP micro-framework that helps you quickly write simple web applications.

Selene attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Routing engine](https://github.com/ovalves/selene/edit/master/README.md).
- [Dependency injection container](https://github.com/ovalves/selene/edit/master/README.md).
- [Session Manager](https://github.com/ovalves/selene/edit/master/README.md).
- [Database ORM](https://github.com/ovalves/selene/edit/master/README.md).
- [Template Engine](https://github.com/ovalves/selene/edit/master/README.md).
- [Middleware engine](https://github.com/ovalves/selene/edit/master/README.md).

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Selene.

```bash
$ composer require vindite/selene "dev-master@dev"
```

This will install Selene and all required dependencies. Selene requires PHP 7.2 or newer.

## Usage

Create an index.php file with the following contents:

```php
<?php

require 'vendor/autoload.php';

// Loading your application folders
$loader = new Selene\Loader\AppLoader;
$loader->addDirectory('App/Controllers');
$loader->addDirectory('App/Models');
$loader->addDirectory('App/Gateway');
$loader->addDirectory('App/Config');
$loader->load();

// Getting an instance of Selene framework
$app = Selene\App::getInstance();

// Using the router to register your application routes
// In this case we are using the authentication middleware
$app->route()->middleware([
    new Selene\Middleware\Handler\Auth
])->group(function () use ($app) {

    // This route responds as callback function
    $app->route()->get('/callable', function () use ($app) {
        $app->json('Hello World!!!');
    });

    // Mapping requested http method with request http path
    $app->route()->get('/', 'HomeController@index');
    $app->route()->get('/show/{id}', 'HomeController@show');
    $app->route()->get('/store', 'HomeController@store');
    $app->route()->get('/login', 'HomeController@login');
    $app->route()->post('/login', 'HomeController@login');
    $app->route()->get('/logout', 'HomeController@logout');
})->run();
```
## Examples

Please see https://github.com/vindite/selene-skeleton for more examples.

## Credits

- [Vinicius Alves](https://github.com/ovalves)

## License

The Selene Microframework is licensed under the MIT license. See [License File](LICENSE) for more information.
