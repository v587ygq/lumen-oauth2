<?php

namespace V587ygq\OAuth\Bridge;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    /**
     * All of the scopes defined for the application.
     *
     * @var array
     */
    public static $scopes = [];

    /**
     * {@inheritdoc}
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        if ('*' === $identifier || \array_key_exists($identifier, static::$scopes)) {
            return new Scope($identifier);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        return $scopes;
    }
}
