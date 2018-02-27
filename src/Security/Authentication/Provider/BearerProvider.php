<?php

namespace Paysera\BearerAuthenticationBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Paysera\BearerAuthenticationBundle\Entity\BearerUserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Paysera\BearerAuthenticationBundle\Security\Authentication\Token\BearerToken;
use Paysera\BearerAuthenticationBundle\Security\Authentication\Token\BearerTokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;

class BearerProvider implements AuthenticationProviderInterface
{
    private $userProvider;

    public function __construct(UserProviderInterface $provider)
    {
        $this->userProvider = $provider;
    }

    public function authenticate(TokenInterface $token)
    {
        /** @var $token BearerTokenInterface $user */
        $user = $this->userProvider->loadUserByUsername($token->getToken());

        if ($user instanceof BearerUserInterface) {
            $authenticatedToken = new BearerToken($user->getRoles());
            $authenticatedToken->setUser($user);
            $authenticatedToken->setToken($user->getToken());
            $authenticatedToken->setAuthenticated(true);

            return $authenticatedToken;
        }
        throw new AuthenticationException('Bearer authentication failed for token ' . $token->getToken());
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof BearerTokenInterface;
    }
}
