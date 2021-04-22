<?php

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Guard\Controller;
use BjyAuthorize\Service\ControllerGuardServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\ControllerGuardServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ControllerGuardServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ControllerGuardServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new ControllerGuardServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $config = [
            'guards' => [
                Controller::class => [],
            ],
        ];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $guard = $factory($container, ControllerGuardServiceFactory::class);

        $this->assertInstanceOf(Controller::class, $guard);
    }
}
