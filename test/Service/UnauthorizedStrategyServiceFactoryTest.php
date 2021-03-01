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

use BjyAuthorize\Service\UnauthorizedStrategyServiceFactory;
use BjyAuthorize\View\UnauthorizedStrategy;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\UnauthorizedStrategyServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class UnauthorizedStrategyServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\UnauthorizedStrategyServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new UnauthorizedStrategyServiceFactory();
        $containerInterface = $this->createMock(ContainerInterface::class);
        $config = [
            'template' => 'foo/bar',
        ];

        $containerInterface
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $strategy = $factory($containerInterface, UnauthorizedStrategyServiceFactory::class);

        $this->assertInstanceOf(UnauthorizedStrategy::class, $strategy);
        $this->assertSame('foo/bar', $strategy->getTemplate());
    }
}
