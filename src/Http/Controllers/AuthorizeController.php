<?php

namespace V587ygq\OAuth\Http\Controllers;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use V587ygq\OAuth\Bridge\User;

class AuthorizeController
{
    /**
     * @var AuthorizationServer
     */
    private $server;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthorizationServer $server)
    {
        $this->server = $server;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            // Validate the HTTP request and return an AuthorizationRequest object.
            $authRequest = $this->server->validateAuthorizationRequest($request);

            // Once the user has logged in set the user on the AuthorizationRequest
            $authRequest->setUser(new User(1));

            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved(true);

            // Return the HTTP redirect response
            return $this->server->completeAuthorizationRequest($authRequest, $response);
        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be formatted into a HTTP response
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
