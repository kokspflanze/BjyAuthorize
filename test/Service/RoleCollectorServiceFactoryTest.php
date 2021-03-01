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

use BjyAuthorize\Collector\RoleCollector;
use BjyAuthorize\Provider\Identity\ProviderInterface;
use BjyAuthorize\Service\RoleCollectorServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\RoleCollectorServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RoleCollectorServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\RoleCollectorServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new RoleCollectorServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $identityProvider = $this->createMock(ProviderInterface::class);

        $container
            ->expects($this->any())
            ->method('get')
            ->with(ProviderInterface::class)
            ->will($this->returnValue($identityProvider));

        $collector = $factory($container, RoleCollectorServiceFactory::class);

        $this->assertInstanceOf(RoleCollector::class, $collector);
    }
}
