<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PersonAdminController extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/person/{id}/change-password", name="person_change_password", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);
        if (!$person) {
            throw $this->createNotFoundException(
                'No person found for id ' . $id
            );
        }

        $form = $this->createFormBuilder()
            ->add('password', PasswordType::class, ['label' => 'Password'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('fos_user.user_manager');

            $data = $form->getData();

            $person->setPlainPassword($data['password']);
            $manager->updateUser($person);

            return $this->redirectToRoute('person_detail', ['id' => $person->getId()]);
        }

        return $this->render('person/change_password.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/person/{id}/toggle-enable", name="person_toggle_enable", methods={"GET", "POST"})
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toggleEnableAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);
        if (!$person) {
            throw $this->createNotFoundException(
                'No person found for id ' . $id
            );
        }

        $manager = $this->get('fos_user.user_manager');

        $person->setEnabled(!$person->isEnabled());
        $manager->updateUser($person);

        return $this->redirectToRoute('person_detail', ['id' => $person->getId()]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/person/{id}/toggle-admin", name="person_toggle_admin", methods={"GET", "POST"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toggleAdminAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);
        if (!$person) {
            throw $this->createNotFoundException(
                'No person found for id ' . $id
            );
        }

        $manager = $this->get('fos_user.user_manager');

        $isAdmin = $person->isSuperAdmin() || $person->hasRole('ROLE_ADMIN');
        if ($isAdmin) {
            $person->setSuperAdmin(false);
            $person->removeRole('ROLE_ADMIN');
        } else {
            $person->setSuperAdmin(true);
            $person->addRole('ROLE_ADMIN');
        }

        $manager->updateUser($person);

        return $this->redirectToRoute('person_detail', ['id' => $person->getId()]);
    }
}