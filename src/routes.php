<?php
$this->app->router->group(['prefix' => 'oauth2', 'namespace' => 'V587ygq\OAuth\Http\Controllers'], function ($router) {
    $router->post('token', 'AccessTokenController');
    $router->delete('token', [
        'middleware' => V587ygq\OAuth\Http\Middleware\ResourceServerMiddleware::class,
        'uses' => 'AccessTokenController@deleteToken',
    ]);
    $router->delete('tokens', [
        'middleware' => V587ygq\OAuth\Http\Middleware\ResourceServerMiddleware::class,
        'uses' => 'AccessTokenController@deleteTokens',
    ]);
    $router->get('authorize', 'AuthorizeController');
    $router->post('authorize', 'AuthorizeController@approve');
});
