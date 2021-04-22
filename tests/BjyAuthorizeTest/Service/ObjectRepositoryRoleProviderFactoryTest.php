<?php

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Provider\Role\ObjectRepositoryProvider;
use BjyAuthorize\Service\ObjectRepositoryRoleProviderFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * {@see \BjyAuthorize\Service\ObjectRepositoryRoleProviderFactory} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ObjectRepositoryRoleProviderFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ObjectRepositoryRoleProviderFactory::__invoke
     */
    public function testInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $entityManager = $this->createMock(ObjectManager::class);
        $repository = $this->createMock(ObjectRepository::class);
        $factory = new ObjectRepositoryRoleProviderFactory();

        $testClassName = 'TheTestClass';

        $config = [
            'role_providers' => [
                ObjectRepositoryProvider::class => [
                    'role_entity_class' => $testClassName,
                    'object_manager' => 'doctrine.entitymanager.orm_default',
                ],
            ],
        ];

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with($testClassName)
            ->will($this->returnValue($repository));

        $container->expects($this->at(0))
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $container->expects($this->at(1))
            ->method('get')
            ->with('doctrine.entitymanager.orm_default')
            ->will($this->returnValue($entityManager));

        $this->assertInstanceOf(
            ObjectRepositoryProvider::class,
            $factory($container, ObjectRepositoryRoleProviderFactory::class)
        );
    }
}
