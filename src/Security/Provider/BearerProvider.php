<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Security\Provider;

use Paysera\BearerAuthenticationBundle\Entity\BearerUserInterface;
use Paysera\BearerAuthenticationBundle\Security\Authentication\Token\BearerTokenInterface;
use Paysera\BearerAuthenticationBundle\Security\Token\BearerToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class BearerProvider implements AuthenticationProviderInterface
{
    private UserProviderInterface $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token): BearerToken
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

    public function supports(TokenInterface $token): bool
    {
        return $token instanceof BearerTokenInterface;
    }
}
