<?php

namespace Paysera\BearerAuthenticationBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class BearerFactory implements SecurityFactoryInterface
{
    public function create(
        ContainerBuilder $container,
        $id,
        $config,
        $userProvider,
        $defaultEntryPoint
    ) {
        $providerId = 'security.authentication.provider.bearer.'.$id;
        $container
            ->setDefinition(
                $providerId,
                new ChildDefinition('paysera_bearer_authentication.security_authentication_provider.bearer_provider')
            )
            ->replaceArgument(0, new Reference($userProvider));

        $listenerId = 'security.authentication.listener.bearer.'.$id;
        $listener = $container->setDefinition(
            $listenerId,
            new ChildDefinition('paysera_bearer_authentication.listener.bearer_listener')
        );

        return [$providerId, $listenerId, $defaultEntryPoint];
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'bearer';
    }

    public function addConfiguration(NodeDefinition $node)
    {

    }
}
