<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Security\User;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Paysera\BearerAuthenticationBundle\Entity\BearerUserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

if (Kernel::MAJOR_VERSION <= 4) {
    class_alias('Symfony\Component\Security\Core\Exception\UsernameNotFoundException', 'Symfony\Component\Security\Core\Exception\UserNotFoundException');
}

class BearerUserProvider implements UserProviderInterface
{
    /**
     * @var HandlerInterface[]
     */
    private array $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    public function addHandler(HandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param string $username
     * @return BearerUserInterface|UserInterface
     * @deprecated use loadUserByIdentifier() instead. this will be removed in future version
     *
     */
    public function loadUserByUsername($username): BearerUserInterface
    {
       return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): BearerUserInterface
    {
        foreach ($this->handlers as $handler) {
            $user = $handler->getByToken($identifier);
            if ($user !== null) {
                return $user;
            }
        }

        throw new UserNotFoundException(sprintf('User with token "%s" does not exist.', $identifier));
    }

    public function refreshUser(UserInterface $user): BearerUserInterface
    {
        if ($user instanceof BearerUserInterface) {
            return $this->loadUserByIdentifier($user->getToken());
        }

        throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
    }

    public function supportsClass($class): bool
    {
        return $class === BearerUserInterface::class;
    }
}
