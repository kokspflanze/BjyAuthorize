<?php

namespace BjyAuthorizeTest\Service;

use \PHPUnit\Framework\TestCase;
use BjyAuthorize\Service\LaminasDbRoleProviderServiceFactory;
use BjyAuthorize\Provider\Role\LaminasDb;
use Interop\Container\ContainerInterface;

/**
 * Test for {@see \BjyAuthorize\Service\LaminasDbRoleProviderServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class LaminasDbRoleProviderServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\LaminasDbRoleProviderServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new LaminasDbRoleProviderServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $config = [
            'role_providers' => [
                LaminasDb::class => [],
            ],
        ];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $guard = $factory($container, LaminasDbRoleProviderServiceFactory::class);

        $this->assertInstanceOf(LaminasDb::class, $guard);
    }
}
