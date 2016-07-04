<?php

namespace AppBundle\Controller\Traits;

use AppBundle\Entity\ProofEntity;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait ProofControllerTrait
{
    /**
     * @param $object
     *
     * @return ProofEntity
     */
    abstract protected function createProof($object);

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
        if (count($data['proof_people']) > 0) {
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
        $imageImporter = $this->get('orbital.image_importer');

        foreach ($data['proof_images'] as $image) {
            $outpath = $imageImporter->persist($image);

            $proof = $this->createProof($object);
            $proof->setImageName($outpath);
            $proof->setPerson($person);

            $em->persist($proof);
        }

        // people
        $personRepository = $this->getDoctrine()->getRepository('AppBundle:Person');
        foreach ($data['proof_people'] as $voucherId) {
            $voucher = $personRepository->find($voucherId);

            $proof = $this->createProof($object);
            $proof->setVoucher($voucher);
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

    /**
     * @param mixed $attributes
     * @param mixed $object
     *
     * @return bool
     */
    abstract protected function isGranted($attributes, $object = null);

    /**
     * @return mixed
     */
    abstract public function getUser();

    /**
     * @param string $id
     *
     * @return object
     */
    abstract public function get($id);

    /**
     * @return Registry
     */
    abstract public function getDoctrine();

    /**
     * @param mixed $data
     * @param array $options
     *
     * @return FormBuilder
     */
    abstract public function createFormBuilder($data = null, array $options = []);
}
