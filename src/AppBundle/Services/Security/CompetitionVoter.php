<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Person;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CompetitionVoter implements VoterInterface
{
    const ENTER = 'ENTER';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [
            self::ENTER,
        ]);
    }

    public function supportsClass($class)
    {
        $supportedClass = 'AppBundle\Entity\Competition';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    public function vote(TokenInterface $token, $competition, array $attributes)
    {
        if (!$this->supportsClass(get_class($competition))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var Competition $competition */

        if (count($attributes) != 1) {
            throw new \InvalidArgumentException('Only one attribute is allowed');
        }

        $user = $token->getUser();
        if (!$user instanceof Person) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch ($attributes[0]) {
            case self::ENTER:
                if ($competition->getInfoOnly()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                if ($competition->hasEntered($user)) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if ($competition->getFreeSpaces() > 0) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}