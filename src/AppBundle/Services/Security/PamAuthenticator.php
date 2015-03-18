<?php


namespace AppBundle\Services\Security;


use AppBundle\Entity\Person;
use AppBundle\Services\Enum\Skill;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

// needed for dev servers
if (!function_exists('pam_auth')) {
    function pam_auth($username, $password)
    {
        return $username == "will" && $password == "will";
    }

    function ldap_get_name($username)
    {
        return "Will Smith";
    }

    function ldap_get_mail($username)
    {
        return "wds12@imperial.ac.uk";
    }
}

class PamAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(UserPasswordEncoderInterface $encoder, Registry $doctrine)
    {
        $this->encoder = $encoder;
        $this->doctrine = $doctrine;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (pam_auth($token->getUsername(), $token->getCredentials())) {
            try {
                $user = $userProvider->loadUserByUsername($token->getUsername());
            } catch (UsernameNotFoundException $e) {
                $user = $this->createUserFromPam($token);
            }
        } else {
            try {
                $user = $userProvider->loadUserByUsername($token->getUsername());
            } catch (UsernameNotFoundException $e) {
                throw new AuthenticationException("Unable to login with the given details");
            }

            $passwordValid = $this->encoder->isPasswordValid($user, $token->getCredentials());
            if(!$passwordValid) {
                throw new AuthenticationException("Unable to login with the given details");
            }
        }


        return new UsernamePasswordToken($user,
            $user->getPassword(),
            $providerKey,
            $user->getRoles());
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    private function createUserFromPam(TokenInterface $token)
    {
        $em = $this->doctrine->getManager();

        $user = new Person();

        $username = $token->getUsername();

        $user->setCuser($username);
        $user->setName(ldap_get_name($username));
        $user->setEmail(ldap_get_mail($username));
        $user->setSkill(Skill::NOVICE);
        $user->setAdmin(false);

        //TODO generate dummy password?

        $em->persist($user);
        $em->flush();

        return $user;
    }
}