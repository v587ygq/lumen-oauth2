<?php

namespace V587ygq\OAuth;

use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use V587ygq\OAuth\Console\ClientCommand;
use V587ygq\OAuth\Console\InstallCommand;

class OAuthServiceProvider extends ServiceProvider
{
    /**
     * The date when access tokens expire.
     *
     * @var \DateInterval
     */
    private static $accessTokenExpireAt;

    /**
     * The date when refresh tokens expire.
     *
     * @var \DateInterval
     */
    private static $refreshTokenExpireAt;

    /**
     * The date when auth codes expire.
     *
     * @var \DateInterval
     */
    private static $authCodeExpireAt;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/oauth2.php', 'oauth2');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        self::$accessTokenExpireAt = new \DateInterval(config('oauth2.access_token_ttl'));
        self::$refreshTokenExpireAt = new \DateInterval(config('oauth2.refresh_token_ttl'));
        self::$authCodeExpireAt = new \DateInterval(config('oauth2.auth_code_ttl'));

        if ($this->app->runningInConsole()) {
            $this->commands([
                ClientCommand::class,
                InstallCommand::class,
            ]);
        }
    }

    /**
     * Register Server.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAuthorizationServer();
        $this->registerResourceServer();
    }

    /**
     * Register the authorization server.
     *
     * @return void
     */
    protected function registerAuthorizationServer()
    {
        $this->app->singleton(AuthorizationServer::class, function () {
            return tap(new AuthorizationServer(
                $this->app->make(Bridge\ClientRepository::class),
                $this->app->make(Bridge\AccessTokenRepository::class),
                $this->app->make(Bridge\ScopeRepository::class),
                new CryptKey(storage_path('app/oauth-private.key'), null, false),
                env('APP_KEY')
            ), function ($server) {
                $server->enableGrantType(
                    $this->makeAuthCodeGrant(),
                    self::$accessTokenExpireAt
                );

                $server->enableGrantType(
                    new ClientCredentialsGrant(),
                    self::$accessTokenExpireAt
                );

                $server->enableGrantType(
                    new ImplicitGrant(self::$accessTokenExpireAt),
                    self::$accessTokenExpireAt
                );

                $server->enableGrantType(
                    $this->makePasswordGrant(),
                    self::$accessTokenExpireAt
                );

                $server->enableGrantType(
                    $this->makeRefreshTokenGrant(),
                    self::$accessTokenExpireAt
                );
            });
        });
    }

    /**
     * Register the resource server.
     *
     * @return void
     */
    protected function registerResourceServer()
    {
        $this->app->singleton(ResourceServer::class, function () {
            return new ResourceServer(
                $this->app->make(Bridge\AccessTokenRepository::class),
                new CryptKey(storage_path('app/oauth-public.key'), null, false)
            );
        });
    }

    /**
     * Create and configure an instance of the Auth Code grant.
     *
     * @return \League\OAuth2\Server\Grant\AuthCodeGrant
     */
    protected function makeAuthCodeGrant()
    {
        $grant = new AuthCodeGrant(
            $this->app->make(Bridge\AuthCodeRepository::class),
            $this->app->make(Bridge\RefreshTokenRepository::class),
            self::$authCodeExpireAt
        );
        $grant->setRefreshTokenTTL(self::$refreshTokenExpireAt);

        return $grant;
    }

    /**
     * Create and configure a Password grant instance.
     *
     * @return \League\OAuth2\Server\Grant\PasswordGrant
     */
    protected function makePasswordGrant()
    {
        $grant = new PasswordGrant(
            $this->app->make(Bridge\UserRepository::class),
            $this->app->make(Bridge\RefreshTokenRepository::class)
        );
        $grant->setRefreshTokenTTL(self::$refreshTokenExpireAt);

        return $grant;
    }

    /**
     * Create and configure a Refresh Token grant instance.
     *
     * @return \League\OAuth2\Server\Grant\RefreshTokenGrant
     */
    protected function makeRefreshTokenGrant()
    {
        $repository = $this->app->make(Bridge\RefreshTokenRepository::class);

        return tap(new RefreshTokenGrant($repository), function ($grant) {
            $grant->setRefreshTokenTTL(self::$refreshTokenExpireAt);
        });
    }
}
