<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Entity;

class BearerUser implements BearerUserInterface
{
    private string $username;
    private string $token;
    private array $roles;

    public function __construct($username, $token, array $roles = [])
    {
        $this->username = $username;
        $this->token = $token;
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        // bearer has token
        return null;
    }

    public function getSalt(): ?string
    {
        // no salt

        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {

    }

    public function getToken(): string
    {
        return $this->token;
    }
}
