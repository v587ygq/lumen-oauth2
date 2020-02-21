# Lumen OAuth2

![License](https://img.shields.io/github/license/v587ygq/lumen-oauth2)
![GitHub issues](https://img.shields.io/github/issues/v587ygq/lumen-oauth2)

## Requirements

- PHP 7.0 or newer
- [Composer](http://getcomposer.org)
- [Lumen](https://lumen.laravel.com/) 6.0 or newer

## Usage

### Installation

Install the package through composer:

```sh
composer require v587ygq/lumen-oauth2
```

Migrate the oauth2 database:

```sh
php artisan migrate
```

### Configure

Add the following lines to  ```bootstrap/app.php```

register the service provider, e.g.
```php
$app->register(V587ygq\OAuth\OAuthServiceProvider::class);
```

register the middleware, e.g.
```php
$app->routeMiddleware([
    'oauth' => V587ygq\OAuth\Http\Middleware\ResourceServerMiddleware::class,
]);
```

### Installed routes

This package will mounts the following routes automatically.

Verb | Path | NamedRoute | Controller | Action | Middleware
--- | --- | --- | --- | --- | ---
POST | /oauth2/token | | V587ygq\OAuth\Http\Controllers\AccessTokenController | __invoke |
DELETE | /oauth2/token | | V587ygq\OAuth\Http\Controllers\AccessTokenController | deleteToken | V587ygq\OAuth\Http\Middleware\ResourceServerMiddleware |
DELETE | /oauth2/tokens | | V587ygq\OAuth\Http\Controllers\AccessTokenController | deleteTokens | V587ygq\OAuth\Http\Middleware\ResourceServerMiddleware |