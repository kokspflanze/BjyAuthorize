<?php

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Provider\Resource\Config;
use BjyAuthorize\Service\ConfigResourceProviderServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\ConfigResourceProviderServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigResourceProviderServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ConfigResourceProviderServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new ConfigResourceProviderServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $config = [
            'resource_providers' => [
                Config::class => [],
            ],
        ];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $guard = $factory($container, ConfigResourceProviderServiceFactory::class);

        $this->assertInstanceOf(Config::class, $guard);
    }
}
