<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Paysera\Component\DependencyInjection\AddTaggedCompilerPass;
use Paysera\BearerAuthenticationBundle\DependencyInjection\Security\Factory\BearerFactory;

class PayseraBearerAuthenticationBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        if (method_exists($extension, 'addAuthenticatorFactory')) {
            $extension->addAuthenticatorFactory(new BearerFactory());
        }

        if (method_exists($extension, 'addSecurityListenerFactory')) {
            $extension->addSecurityListenerFactory(new BearerFactory());
        }

        $container->addCompilerPass(new AddTaggedCompilerPass(
            'paysera_bearer_authentication.security_user.bearer_user_provider',
            'paysera_bearer_authentication.handler',
            'addHandler'
        ));
    }
}
