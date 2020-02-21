<?php
namespace V587ygq\OAuth\Bridge;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class Scope implements ScopeEntityInterface
{
    use EntityTrait;

    public static $scopes = [];

    /**
     * Scope constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->setIdentifier($name);
    }

    /**
     * @param $id
     * @return bool
     */
    public static function hasScope($id)
    {
        return $id === '*' || array_key_exists($id, static::$scopes);
    }

    /**
     * Get the data that should be serialized to JSON.
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
