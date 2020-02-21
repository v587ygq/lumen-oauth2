<?php
namespace V587ygq\OAuth\Http\Controllers;

use Illuminate\Http\Request;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use V587ygq\OAuth\Models\AccessToken;

class AccessTokenController
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

    /**
     * Authorize a client to access the user's account.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ServerRequestInterface $request, Response $response)
    {
        try {
            $psrResponse = $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $e) {
            $psrResponse = $e->generateHttpResponse($response);
        }
        return response(
            $psrResponse->getBody(),
            $psrResponse->getStatusCode(),
            $psrResponse->getHeaders()
        );
    }

    /**
     * Delete the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteToken(Request $request)
    {
        AccessToken::find($request->get('oauth_access_token_id'))->delete();
        return response(null, 204);
    }

    /**
     * Delete all of the user's tokens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteTokens(Request $request)
    {
        AccessToken::where('user_id', $request->get('oauth_user_id'))->delete();
        return response(null, 204);
    }
}
