<?php
namespace V587ygq\OAuth\Bridge;

use App\User as UserModel;
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
        $user = (new UserModel)->getUserByPassword($username, $password);
        return !$user ?: new User($user->getAuthIdentifier());
    }
}
