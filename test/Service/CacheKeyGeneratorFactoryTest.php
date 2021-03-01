<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\CacheKeyGeneratorFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnit tests for {@see \BjyAuthorize\Service\CacheKeyGeneratorFactory}
 *
 * @author Steve Rhoades <sedonami@gmail.com>
 */
class CacheKeyGeneratorFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\CacheKeyGeneratorFactory::__invoke
     */
    public function testInvokeReturnsDefaultCallable()
    {
        $container = $this->createMock(ContainerInterface::class);
        $config = [];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $factory = new CacheKeyGeneratorFactory();

        $cacheKeyGenerator = $factory($container, CacheKeyGeneratorFactory::class);
        $this->assertTrue(is_callable($cacheKeyGenerator));
        $this->assertEquals('bjyauthorize_acl', $cacheKeyGenerator());
    }

    /**
     * @covers \BjyAuthorize\Service\CacheKeyGeneratorFactory::__invoke
     */
    public function testInvokeReturnsCacheKeyGeneratorCallable()
    {
        $container = $this->createMock(ContainerInterface::class);
        $config = [
            'cache_key' => 'some_new_value'
        ];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $factory = new CacheKeyGeneratorFactory();

        $cacheKeyGenerator = $factory($container, CacheKeyGeneratorFactory::class);
        $this->assertTrue(is_callable($cacheKeyGenerator));
        $this->assertEquals('some_new_value', $cacheKeyGenerator());
    }
}
