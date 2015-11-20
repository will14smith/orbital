<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\CompetitionSession;
use AppBundle\Entity\Person;
use AppBundle\Services\Competitions\CompetitionManager;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CompetitionSessionVoter extends BaseVoter
{
    protected $attributes = [
        SecurityAction::ENTER
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
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
