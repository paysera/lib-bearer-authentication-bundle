<?php

namespace Paysera\BearerAuthenticationBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface BearerUserInterface extends UserInterface
{
    /**
     * @return string
     */
    public function getToken();
}
