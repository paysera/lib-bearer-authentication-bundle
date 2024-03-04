<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface BearerTokenInterface extends TokenInterface
{
    /**
     * @return string
     */
    public function getToken(): string;
}
