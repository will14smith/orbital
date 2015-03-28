<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProofEntity;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class ProofController extends Controller
{
    /**
     * @param $object
     *
     * @return ProofEntity
     */
    protected abstract function createProof($object);

    protected function handleProof(FormInterface $form)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }

        if (!$form->isSubmitted()) {
            return;
        }

        $data = $form->getData();
        if (count($data['proof_images']) > 0) {
            return;
        }
        if (trim($data['proof_notes'])) {
            return;
        }

        $form->addError(new FormError('Expecting some proof'));
    }

    protected function saveProof(ObjectManager $em, $object, FormInterface $form)
    {
        $person = $this->getUser();
        $data = $form->getData();

        // images
        $image_importer = $this->get('orbital.image_importer');

        foreach ($data['proof_images'] as $image) {
            $outpath = $image_importer->persist($image);

            $proof = $this->createProof($object);
            $proof->setImageName($outpath);
            $proof->setPerson($person);

            $em->persist($proof);
        }

        // notes
        $notes = trim($data['proof_notes']);
        if (!empty($notes)) {

            $proof = $this->createProof($object);
            $proof->setNotes($notes);
            $proof->setPerson($person);

            $em->persist($proof);
        }
    }

    /**
     * @param Request $request
     *
     * @return bool|\Symfony\Component\HttpFoundation\Response
     */
    protected function confirmProof(Request $request)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return false;
        }

        return $form->createView();
    }
}