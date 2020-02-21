<?php
namespace V587ygq\OAuth;

use Illuminate\Support\ServiceProvider;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\AuthorizationServer;
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
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'oauth2');

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
                new CryptKey(base_path().'/oauth-private.key', null, false),
                env('APP_KEY')
            ), function ($server) {
                $server->enableGrantType(
                    $this->makeAuthCodeGrant(), new \DateInterval('PT1H')
                );

                $server->enableGrantType(
                    new ClientCredentialsGrant, new \DateInterval('PT1H')
                );

                $server->enableGrantType(
                    new ImplicitGrant(new \DateInterval('PT1H')), new \DateInterval('PT1H')
                );

                $server->enableGrantType(
                    $this->makePasswordGrant(), new \DateInterval('PT1H')
                );

                $server->enableGrantType(
                    $this->makeRefreshTokenGrant(), new \DateInterval('PT1H')
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
                new CryptKey(base_path().'/oauth-public.key', null, false)
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
            new \DateInterval('PT10M')
        );
        $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
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
        $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
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
            $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
        });
    }
}
