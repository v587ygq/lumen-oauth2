<?php
namespace V587ygq\OAuth\Bridge;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\ScopeTrait;

class Scope implements ScopeEntityInterface
{
    use EntityTrait, ScopeTrait;

    /**
     * Create a new scope instance.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct($name)
    {
        $this->setIdentifier($name);
    }
}
