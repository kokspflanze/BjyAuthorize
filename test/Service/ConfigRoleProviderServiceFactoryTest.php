<?php

namespace BjyAuthorizeTest\Service;

use \PHPUnit\Framework\TestCase;
use BjyAuthorize\Service\ConfigRoleProviderServiceFactory;
use BjyAuthorize\Provider\Role\Config;
use Interop\Container\ContainerInterface;

/**
 * Test for {@see \BjyAuthorize\Service\ConfigRoleProviderServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigRoleProviderServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ConfigRoleProviderServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new ConfigRoleProviderServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $config = [
            'role_providers' => [
                Config::class => [],
            ],
        ];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $guard = $factory($container,ConfigRoleProviderServiceFactory::class);

        $this->assertInstanceOf(Config::class, $guard);
    }
}
