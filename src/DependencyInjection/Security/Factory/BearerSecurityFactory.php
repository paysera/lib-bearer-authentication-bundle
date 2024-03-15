<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

/**
 * @deprecated since symfony 5.4.x, use BearerAuthenticatorFactory instead
 */
class BearerSecurityFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint): array
    {
        $providerId = 'security.authentication.provider.bearer.'.$id;
        $container
            ->setDefinition(
                $providerId,
                new ChildDefinition('paysera_bearer_authentication.security_authentication_provider.bearer_provider')
            )
            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'security.authentication.listener.bearer.'.$id;

        $container->setDefinition(
            $listenerId,
            new ChildDefinition('paysera_bearer_authentication.listener.bearer_listener')
        );

        return [
            $providerId,
            $listenerId,
            $defaultEntryPoint,
        ];
    }

    public function getPosition(): string
    {
        return 'pre_auth';
    }

    public function getKey(): string
    {
        return 'bearer';
    }

    public function addConfiguration(NodeDefinition $builder): void
    {

    }
}
