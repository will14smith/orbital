<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\Person;
use AppBundle\Entity\Score;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ScoringVoter implements VoterInterface
{
    const JUDGE = 'JUDGE';
    const SCORE = 'SCORE';
    const SIGN = 'SIGN';
    
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [
            self::JUDGE,
            self::SCORE,
            self::SIGN,
        ]);
    }

    public function supportsClass($class)
    {
        $supportedClass = 'AppBundle\Entity\Score';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    public function vote(TokenInterface $token, $score, array $attributes)
    {
        if (!$this->supportsClass(get_class($score))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var Score $score */

        if (count($attributes) != 1) {
            throw new \InvalidArgumentException('Only one attribute is allowed');
        }

        $user = $token->getUser();
        if (!$user instanceof Person) {
            return VoterInterface::ACCESS_DENIED;
        }

        // can't do much with an accepted score
        if ($score->getDateAccepted()) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch ($attributes[0]) {
            case self::JUDGE:
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                //TODO handle judges in competition

                break;
            case self::SCORE:
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                //TODO handle delegated scorers, non-self scoring, etc...
                if ($score->getPerson()->getId() == $user->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                break;
            case self::SIGN:
                if (in_array('ROLE_ADMIN', $user->getRoles())) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                //TODO might need scorer signature too?
                if ($score->getPerson()->getId() == $user->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}