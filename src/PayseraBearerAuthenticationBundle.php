<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle;

use Paysera\BearerAuthenticationBundle\DependencyInjection\Security\Factory\BearerAuthenticatorFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Paysera\Component\DependencyInjection\AddTaggedCompilerPass;
use Paysera\BearerAuthenticationBundle\DependencyInjection\Security\Factory\BearerSecurityFactory;

class PayseraBearerAuthenticationBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        if (method_exists($extension, 'addAuthenticatorFactory')) {
            $extension->addAuthenticatorFactory(new BearerAuthenticatorFactory());
        }

        if (method_exists($extension, 'addSecurityListenerFactory')) {
            $extension->addSecurityListenerFactory(new BearerSecurityFactory());
        }

        $container->addCompilerPass(new AddTaggedCompilerPass(
            'paysera_bearer_authentication.security_user.bearer_user_provider',
            'paysera_bearer_authentication.handler',
            'addHandler'
        ));
    }
}
