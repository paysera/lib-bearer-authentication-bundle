<?php

namespace Paysera\BearerAuthenticationBundle\Entity;

class BearerUser implements BearerUserInterface
{
    private $username;
    private $token;
    private $roles;

    public function __construct($username, $token, array $roles = [])
    {
        $this->username = $username;
        $this->token = $token;
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        // bearer has token
    }

    public function getSalt()
    {
        // no salt
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {

    }

    public function getToken()
    {
        return $this->token;
    }
}
