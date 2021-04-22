<?php

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\ConfigServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\ConfigServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ConfigServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new ConfigServiceFactory();
        $container = $this->createMock(ContainerInterface::class);

        $container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue(['bjyauthorize' => ['foo' => 'bar']]));

        $this->assertSame(['foo' => 'bar'], $factory($container, ConfigServiceFactory::class));
    }
}
