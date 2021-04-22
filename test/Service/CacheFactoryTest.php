<?php

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\CacheFactory;
use Interop\Container\ContainerInterface;
use Laminas\Cache\Storage\Adapter\Memory;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnit tests for {@see \BjyAuthorize\Service\CacheFactory}
 *
 * @author Christian Bergau <cbergau86@gmail.com>
 */
class CacheFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\CacheFactory::__invoke
     */
    public function testInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $config = [
            'cache_options' => [
                'adapter' => [
                    'name' => 'memory',
                ],
                'plugins' => [
                    'serializer',
                ]
            ]
        ];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $factory = new CacheFactory();

        $this->assertInstanceOf(Memory::class, $factory($container, CacheFactory::class));
    }
}
