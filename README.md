# Lumen OAuth2

![License](https://img.shields.io/github/license/v587ygq/lumen-oauth2)
![GitHub issues](https://img.shields.io/github/issues/v587ygq/lumen-oauth2)
![GitHub tag (latest SemVer)](https://img.shields.io/github/v/tag/v587ygq/lumen-oauth2)

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

### Configure

Migrate the oauth2 database:

```sh
php artisan migrate
```

Generating public and private keys

```sh
php artisan oauth2:install
```

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

you should defind getUserEntityByUserCredentials function on your user model, e.g.

```php
class User extends Model implements AuthenticatableContract, AuthorizableContract {
    use Authenticatable, Authorizable;

    ......

    public function getUserByPassword($username, $password, $type='email') {
        // return User
    }
}
```

### Installed routes

This package will mounts the following routes automatically.

Verb | Path | NamedRoute | Controller | Action | Middleware
--- | --- | --- | --- | --- | ---
get | /oauth2/authorize | | V587ygq\OAuth\Http\Controllers\AuthorizeController | __invoke |
POST | /oauth2/token | | V587ygq\OAuth\Http\Controllers\AccessTokenController | __invoke |
DELETE | /oauth2/token | | V587ygq\OAuth\Http\Controllers\AccessTokenController | deleteToken | V587ygq\OAuth\Http\Middleware\ResourceServerMiddleware |
DELETE | /oauth2/tokens | | V587ygq\OAuth\Http\Controllers\AccessTokenController | deleteTokens | V587ygq\OAuth\Http\Middleware\ResourceServerMiddleware |