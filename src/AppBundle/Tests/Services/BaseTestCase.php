<?php

namespace AppBundle\Tests\Services;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase {
    public function getMockBuilder($name) {
        return parent::getMockBuilder($name)
            ->disableOriginalConstructor()
            ->disableProxyingToOriginalMethods();
    }

    protected function getDoctrine() {
        return $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')->getMock();
    }
    protected function getRepository($doctrine, $name, $type = null) {
        if($type === null) {
            $type = '\Doctrine\ORM\EntityRepository';
        } else if($type === true) {
            $type = '\\' . str_replace(':', '\Entity\\', $name) . 'Repository';
        }

        $repository = $this->getMockBuilder($type)->getMock();

        $doctrine->expects($this->any())
            ->method('getRepository')
            ->with($this->stringEndsWith($name))
            ->will($this->returnValue($repository));

        return $repository;
    }
}
