<?php

namespace Paysera\BearerAuthenticationBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class BearerToken extends AbstractToken implements BearerTokenInterface
{
    protected $token;

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return '';
    }
}
