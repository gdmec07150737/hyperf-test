<?php

declare(strict_types=1);

namespace App\Authorization\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    use ClientTrait, EntityTrait;

    /**
     * ClientEntity constructor.
     *
     * @param string|int $identifier
     * @param string $name
     * @param string $redirectUri
     * @param bool $isConfidential
     */
    public function __construct($identifier, string $name, string $redirectUri, bool $isConfidential = false)
    {
        $this->setIdentifier($identifier);
        $this->name = $name;
        $this->redirectUri = explode(',', $redirectUri);
        $this->isConfidential = $isConfidential;
    }
}
