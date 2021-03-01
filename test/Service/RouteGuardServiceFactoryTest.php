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

use BjyAuthorize\Guard\Route;
use BjyAuthorize\Service\RouteGuardServiceFactory;
use BjyAuthorize\Service\UnauthorizedStrategyServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\RouteGuardServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RouteGuardServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\RouteGuardServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new RouteGuardServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $config = [
            'guards' => [
                Route::class => [],
            ],
        ];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $guard = $factory($container, UnauthorizedStrategyServiceFactory::class);

        $this->assertInstanceOf(Route::class, $guard);
    }
}
