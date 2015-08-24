<?php

namespace AppBundle\Form\Type\Custom;

use Symfony\Component\Form\AbstractType;

class SelectType extends AbstractType
{
    private $_isEntity;

    public function __construct($isEntity) {
        $this->_isEntity = $isEntity;
    }

    public function getParent()
    {
        return $this->_isEntity ? 'entity' : 'choice';
    }

    public function getName()
    {
        return 'select';
    }
}