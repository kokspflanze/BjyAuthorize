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
