<?php

namespace AppBundle\Tests\Services;

abstract class ServiceTestCase extends \PHPUnit_Framework_TestCase
{
    public function getMockBuilder($name)
    {
        return parent::getMockBuilder($name)
            ->disableOriginalConstructor()
            ->disableProxyingToOriginalMethods();
    }

    /**
     * @param int $flushCount
     *
     * @return \Doctrine\Bundle\DoctrineBundle\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDoctrine($flushCount = null)
    {
        $doctrine = $this->getMockBuilder('\Doctrine\Bundle\DoctrineBundle\Registry')->getMock();
        $entityManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')->getMock();

        $doctrine->expects($this->any())
            ->method('getManager')->willReturn($entityManager);

        if ($flushCount !== null) {
            $entityManager->expects($this->exactly($flushCount))
                ->method('flush');
        }

        $doctrine->repositories = [];
        $doctrine->expects($this->any())
            ->method('getRepository')
            ->willReturnCallback(function (string $name) use ($doctrine) {
                if (!array_key_exists($name, $doctrine->repositories)) {
                    throw new \Exception("Repository not found");
                }

                return $doctrine->repositories[$name];
            });

        return $doctrine;
    }

    protected function getRepository(\PHPUnit_Framework_MockObject_MockObject $doctrine, $name, $type = null)
    {
        if ($type === null) {
            $type = '\Doctrine\ORM\EntityRepository';
        } elseif ($type === true) {
            $type = '\\' . str_replace(':', '\Entity\\', $name) . 'Repository';
        }

        $repository = $this->getMockBuilder($type)->getMock();

        $doctrine->repositories[$name] = $repository;

        return $repository;
    }
}
