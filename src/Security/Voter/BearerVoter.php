<?php

namespace Paysera\BearerAuthenticationBundle\Security\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use Paysera\BearerAuthenticationBundle\Entity\BearerUserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class BearerVoter implements VoterInterface
{
    const ROLE_API = 'ROLE_API';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [self::ROLE_API], true);
    }

    public function supportsClass($class)
    {
        return true;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!in_array(self::ROLE_API, $attributes, true)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var UserInterface $user */
        $user = $token->getUser();

        if ($user instanceof BearerUserInterface && $token->isAuthenticated()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
