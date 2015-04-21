<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\League;
use AppBundle\Entity\Person;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class LeagueVoter implements VoterInterface
{
    const SIGNUP = 'SIGNUP';
    const SUBMIT = 'SUBMIT';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [
            self::SIGNUP,
            self::SUBMIT,
        ]);
    }

    public function supportsClass($class)
    {
        $supportedClass = 'AppBundle\Entity\League';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    public function vote(TokenInterface $token, $league, array $attributes)
    {
        if (!$this->supportsClass(get_class($league))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var League $league */

        if (count($attributes) != 1) {
            throw new \InvalidArgumentException('Only one attribute is allowed');
        }

        $user = $token->getUser();
        if (!$user instanceof Person) {
            return VoterInterface::ACCESS_DENIED;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return VoterInterface::ACCESS_GRANTED;
        }

        switch ($attributes[0]) {
            case self::SIGNUP:
                if($league->canSignup($user) && !$league->isSignedUp($user)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case self::SUBMIT:
                if($league->isSignedUp($user)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
