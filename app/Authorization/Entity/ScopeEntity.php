<?php

declare(strict_types=1);

namespace App\Authorization\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ScopeEntity implements ScopeEntityInterface
{
    use EntityTrait;

    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
