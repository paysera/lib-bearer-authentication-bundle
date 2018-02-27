<?php

namespace Paysera\BearerAuthenticationBundle\Security\User;

use Paysera\BearerAuthenticationBundle\Entity\BearerUserInterface;

interface HandlerInterface
{
    /**
     * @param string
     *
     * @return BearerUserInterface|null
     */
    public function getByToken($param);
}
