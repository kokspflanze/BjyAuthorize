<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */

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
