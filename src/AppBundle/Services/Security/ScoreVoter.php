<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\Person;
use AppBundle\Entity\Score;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ScoreVoter extends BaseVoter
{
    protected $attributes = [SecurityAction::ACCEPT, SecurityAction::DELETE, SecurityAction::EDIT];
    protected $class = 'AppBundle\Entity\Score';

    /**
     * {@inheritdoc}
     */
    protected function voteInternal(Person $user, $score, $permission)
    {
        /* @var Score $score */

        switch ($permission) {
            case SecurityAction::EDIT:
                if ($user->isAdmin()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                if ($score->getPerson()->getId() == $user->getId()) {
                    if (!$score->getDateAccepted()) {
                        return VoterInterface::ACCESS_GRANTED;
                    }
                }

                break;
            case SecurityAction::ACCEPT:
                if (!$user->isAdmin()) {
                    return VoterInterface::ACCESS_DENIED;
                }
                if ($score->getDateAccepted()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                return VoterInterface::ACCESS_GRANTED;
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
