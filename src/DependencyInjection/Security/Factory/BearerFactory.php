<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class BearerFactory implements AuthenticatorFactoryInterface, SecurityFactoryInterface
{
    /**
     * @deprecated since symfony 5.4.x, use AuthenticatorFactoryInterface instead
     */
    public function create(ContainerBuilder $container, $id, $config, $userProviderId, $defaultEntryPointId): array
    {
        $providerId = 'security.authentication.provider.bearer.'.$id;
        $container
            ->setDefinition(
                $providerId,
                new ChildDefinition('paysera_bearer_authentication.security_authentication_provider.bearer_provider')
            )
            ->replaceArgument(0, new Reference($userProvider));

        $listenerId = 'security.authentication.listener.bearer.'.$id;

        $container->setDefinition(
            $listenerId,
            new ChildDefinition('paysera_bearer_authentication.listener.bearer_listener')
        );

        return [
            $providerId,
            $listenerId,
            $defaultEntryPointId,
        ];
    }

    /**
     *
     * @deprecated since Symfony 5.4, use AuthenticatorFactoryInterface instead
     */
    public function getPosition(): string
    {
        return 'pre_auth';
    }

    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): string
    {
        $authenticatorId = 'security.authentication.provider.bearer.' . $firewallName;

        $authenticator = (new ChildDefinition('paysera_bearer_authentication.authenticator.bearer_passport'));

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
