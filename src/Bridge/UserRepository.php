<?php

namespace V587ygq\OAuth\Bridge;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use RuntimeException;

class UserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        if (null === ($model = config('oauth2.user_model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        $user = (new $model())->getUserByPassword($username, $password);

        return !$user ?: new User($user->getAuthIdentifier());
    }
}
