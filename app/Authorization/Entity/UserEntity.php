<?php

declare(strict_types=1);

namespace App\Authorization\Entity;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{
    use EntityTrait;

    /**
     * UserEntity constructor.
     * @param $identifier
     */
    public function __construct($identifier)
    {
        $this->setIdentifier($identifier);
    }
}
