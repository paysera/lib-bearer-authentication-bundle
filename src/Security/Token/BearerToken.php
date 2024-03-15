<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Security\Token;

use Paysera\BearerAuthenticationBundle\Security\Authentication\Token\BearerTokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class BearerToken extends AbstractToken implements BearerTokenInterface
{
    protected string $token;

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getCredentials(): string
    {
        return '';
    }
}
