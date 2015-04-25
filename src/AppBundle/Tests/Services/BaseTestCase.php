<?php

namespace AppBundle\Tests\Services;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase {
    public function getMockBuilder($name) {
        return parent::getMockBuilder($name)
            ->disableOriginalConstructor()
            ->disableProxyingToOriginalMethods();
    }

    /**
     * @param int $flushCount
     *
     * @return \Doctrine\Bundle\DoctrineBundle\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDoctrine($flushCount = null) {
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')->getMock();
        $entityManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')->getMock();

        $doctrine->expects($this->any())
            ->method('getManager')->willReturn($entityManager);

        if($flushCount !== null) {
            $entityManager->expects($this->exactly($flushCount))
                ->method('flush');
        }

        return $doctrine;
    }
    protected function getRepository(\PHPUnit_Framework_MockObject_MockObject $doctrine, $name, $type = null) {
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
