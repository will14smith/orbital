<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\Badge;
use AppBundle\Entity\Person;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class BadgeVoter implements VoterInterface
{
    const CLAIM = 'CLAIM';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::CLAIM,
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'AppBundle\Entity\Badge';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    public function vote(TokenInterface $token, $badge, array $attributes)
    {
        if (!$this->supportsClass(get_class($badge))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var Badge $badge */

        if (count($attributes) != 1) {
            throw new \InvalidArgumentException('Only one attribute is allowed');
        }

        $user = $token->getUser();
        if (!$user instanceof Person) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch ($attributes[0]) {
            case self::CLAIM:
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                if ($badge->getMultiple()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                //TODO has user been awarded this badge yet?
                return VoterInterface::ACCESS_GRANTED;

                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}