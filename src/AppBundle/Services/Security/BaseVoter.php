<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\Person;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

abstract class BaseVoter implements VoterInterface
{
    protected $attributes = [];
    protected $class = '';

    /**
     * @inheritdoc
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, $this->attributes);
    }

    /**
     * @inheritdoc
     */
    public function supportsClass($class)
    {
        return $this->class === $class || is_subclass_of($class, $this->class);
    }

    /**
     * @inheritdoc
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsClass(get_class($object))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (count($attributes) != 1) {
            throw new \InvalidArgumentException('Only one attribute is allowed');
        }

        $user = $token->getUser();
        if (!$user instanceof Person) {
            return VoterInterface::ACCESS_DENIED;
        }

        $result = $this->voteInternal($user, $object, $attributes[0]);

        return $result ?: VoterInterface::ACCESS_DENIED;
    }

    /**
     * @param Person $user
     * @param object|null $object
     * @param string $permission
     *
     * @return int|null either ACCESS_GRANTED, ACCESS_ABSTAIN, ACCESS_DENIED, or null
     */
    protected abstract function voteInternal(Person $user, $object, $permission);
}
