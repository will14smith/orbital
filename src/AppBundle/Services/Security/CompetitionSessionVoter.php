<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\CompetitionSession;
use AppBundle\Entity\Person;
use AppBundle\Services\Competitions\CompetitionManager;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CompetitionSessionVoter extends BaseVoter
{
    protected $attributes = [
        SecurityAction::ENTER,
        SecurityAction::START,
        SecurityAction::END,
        SecurityAction::SCORE
    ];
    protected $class = 'AppBundle\Entity\CompetitionSession';

    /**
     * @inheritDoc
     */
    protected function voteInternal(Person $user, $session, $permission)
    {
        /** @var CompetitionSession $session */
        $competition = $session->getCompetition();

        if (!$competition->getHosted()) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch ($permission) {
            case SecurityAction::ENTER:
                if($session->isFinished()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if ($user->isAdmin()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                if (!$competition->isOpen()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if (CompetitionManager::hasEntered($session, $user)) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if (CompetitionManager::getFreeSpaces($session) > 0) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                break;
            case SecurityAction::START:
                if (!$user->isAdmin()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if ($competition->isOpen()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if ($session->isStarted()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                return VoterInterface::ACCESS_GRANTED;
            case SecurityAction::SCORE:
                if (!$user->isAdmin()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if (!$session->isStarted()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                return VoterInterface::ACCESS_GRANTED;
            case SecurityAction::END:
                if (!$user->isAdmin()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if (!$session->isStarted()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
