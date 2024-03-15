<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Paysera\BearerAuthenticationBundle\Security\BearerPassportAuthenticator;

class BearerAuthenticatorFactory implements AuthenticatorFactoryInterface
{
    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): string
    {
        $authenticatorId = 'security.authentication.provider.bearer.' . $firewallName;

        $authenticator = (new ChildDefinition(BearerPassportAuthenticator::class));

        $container->setDefinition($authenticatorId, $authenticator);

        return $authenticatorId;
    }

    public function getPriority(): int
    {
        return -10;
    }

    public function getKey(): string
    {
        return 'bearer';
    }

    public function addConfiguration(NodeDefinition $builder): void
    {

    }
}
