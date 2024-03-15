<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Security\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use Paysera\BearerAuthenticationBundle\Entity\BearerUserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class BearerVoter implements VoterInterface
{
    private const ROLE_API = 'ROLE_API';

    public function supportsAttribute($attribute): bool
    {
        return $attribute === self::ROLE_API;
    }

    public function supportsClass($class): bool
    {
        return true;
    }

    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        if (!in_array(self::ROLE_API, $attributes, true)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var UserInterface $user */
        $user = $token->getUser();

        if ($user instanceof BearerUserInterface && $token->isAuthenticated() !== null) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
