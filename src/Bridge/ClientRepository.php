<?php

namespace V587ygq\OAuth\Bridge;

use Illuminate\Support\Facades\Hash;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use V587ygq\OAuth\Models\Client as ClientModel;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier)
    {
        $client = ClientModel::find($clientIdentifier);

        if (!$client || $client->revoked) {
            return;
        }

        return new Client($clientIdentifier, $client->name, $client->redirect, $client->confidential);
    }

    /**
     * {@inheritdoc}
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        $client = ClientModel::find($clientIdentifier);
        if ($client && $client->grant_type === $grantType && Hash::check($clientSecret, $client->secret)) {
            return true;
        }

        return false;
    }
}
