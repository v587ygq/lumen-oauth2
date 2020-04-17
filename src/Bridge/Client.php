<?php

namespace V587ygq\OAuth\Bridge;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class Client implements ClientEntityInterface
{
    use ClientTrait;
    use EntityTrait;

    /**
     * Create a new client instance.
     *
     * @param string $identifier
     * @param string $name
     * @param string $redirectUri
     * @param bool   $isConfidential
     *
     * @return void
     */
    public function __construct($identifier, $name, $redirectUri, $isConfidential = false)
    {
        $this->setIdentifier($identifier);
        $this->name = $name;
        $this->redirectUri = explode(',', $redirectUri);
        $this->isConfidential = $isConfidential;
    }
}
