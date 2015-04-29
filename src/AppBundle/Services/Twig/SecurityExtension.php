<?php


namespace AppBundle\Services\Twig;

use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityExtension extends \Twig_Extension {

    private $securityChecker;

    public function __construct(AuthorizationCheckerInterface $securityChecker = null)
    {
        $this->securityChecker = $securityChecker;
    }

    public function isGrantedAny(array $roles, $object = null, $field = null)
    {
        if (null === $this->securityChecker) {
            return false;
        }
        if (null !== $field) {
            $object = new FieldVote($object, $field);
        }

        foreach($roles as $role) {
            if($this->securityChecker->isGranted($role, $object)) {
                return true;
            }
        }

        return false;
    }
    public function isGrantedAll(array $roles, $object = null, $field = null)
    {
        if (null === $this->securityChecker) {
            return false;
        }
        if (null !== $field) {
            $object = new FieldVote($object, $field);
        }

        foreach($roles as $role) {
            if(!$this->securityChecker->isGranted($role, $object)) {
                return false;
            }
        }

        return count($roles) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('is_granted_all', [$this, 'isGrantedAll']),
            new \Twig_SimpleFunction('is_granted_any', [$this, 'isGrantedAny']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orbital-security';
    }
}
