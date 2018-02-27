<?php

namespace Paysera\BearerAuthenticationBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Paysera\BearerAuthenticationBundle\Entity\BearerUserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class BearerUserProvider implements UserProviderInterface
{
    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    public function addHandler(HandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    public function loadUserByUsername($token)
    {
        foreach ($this->handlers as $handler) {
            $user = $handler->getByToken($token);
            if ($user !== null) {
                return $user;
            }
        }
        throw new UsernameNotFoundException(sprintf('User with token "%s" does not exist.', $token));
    }

    public function refreshUser(UserInterface $user)
    {
        if ($user instanceof BearerUserInterface) {
            return $this->loadUserByUsername($user->getToken());
        }
        throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
    }

    public function supportsClass($class)
    {
        return $class === BearerUserInterface::class;
    }
}
