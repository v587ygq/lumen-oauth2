<?php
namespace V587ygq\OAuth\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use V587ygq\OAuth\Bridge\User;
use V587ygq\OAuth\Models\Client;

class AuthorizeController
{
    /**
     * @var AuthorizationServer
     */
    private $server;

    /**
     * Create a new controller instance.
     *
     * @param  \League\OAuth2\Server\AuthorizationServer  $server
     * @return void
     */
    public function __construct(AuthorizationServer $server)
    {
        $this->server = $server;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) {
        try {
            $authRequest = $this->server->validateAuthorizationRequest($request);

            $scopes = $authRequest->getScopes();
            $authRequest->setUser(new User($request->getAttributes('oauth_user_id')));
            $authRequest->setAuthorizationApproved(true);
            return $server->completeAuthorizationRequest($authRequest, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            return $response;
        }
    }
}
