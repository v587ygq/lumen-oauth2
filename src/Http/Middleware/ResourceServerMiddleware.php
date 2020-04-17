<?php

namespace V587ygq\OAuth\Http\Middleware;

use Closure;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class ResourceServerMiddleware
{
    /**
     * @var ResourceServer
     */
    private $server;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ResourceServer $server)
    {
        $this->server = $server;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $psr17Factory = new Psr17Factory();
        $psrRequest = (new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory))->createRequest($request);
        try {
            $psrRequest = $this->server->validateAuthenticatedRequest($psrRequest);
        } catch (OAuthServerException $e) {
            $psrResponse = $e->generateHttpResponse(new Response());

            return response(
                $psrResponse->getBody(),
                $psrResponse->getStatusCode(),
                $psrResponse->getHeaders()
            );
        }

        return $next($request->merge([
            'oauth_access_token_id' => $psrRequest->getAttribute('oauth_access_token_id'),
            'oauth_client_id' => $psrRequest->getAttribute('oauth_client_id'),
            'oauth_user_id' => $psrRequest->getAttribute('oauth_user_id'),
            'oauth_scopes' => $psrRequest->getAttribute('oauth_scopes'),
        ]));
    }
}
