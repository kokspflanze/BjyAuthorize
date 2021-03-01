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

use BjyAuthorize\Service\Authorize;
use BjyAuthorize\Service\AuthorizeAwareInterface;
use BjyAuthorize\Service\AuthorizeAwareServiceInitializer;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\AuthorizeAwareServiceInitializer}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class AuthorizeAwareServiceInitializerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $authorize;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $container;

    /**
     * @var \BjyAuthorize\Service\AuthorizeAwareServiceInitializer
     */
    protected $initializer;

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->authorize = $this->getMockBuilder(Authorize::class)->disableOriginalConstructor()->getMock();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->initializer = new AuthorizeAwareServiceInitializer();

        $this->container->expects($this->any())->method('get')->will($this->returnValue($this->authorize));
    }

    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        unset($this->initializer);
        unset($this->container);
        unset($this->authorize);
    }

    /**
     * @covers \BjyAuthorize\Service\AuthorizeAwareServiceInitializer::initialize
     */
    public function testInitializeWithAuthorizeAwareObject()
    {
        $awareObject = $this->createMock(AuthorizeAwareInterface::class);

        $awareObject->expects($this->once())->method('setAuthorizeService')->with($this->authorize);

        $initializer = $this->initializer;
        $initializer($this->container, $awareObject);
    }

    /**
     * @covers \BjyAuthorize\Service\AuthorizeAwareServiceInitializer::initialize
     */
    public function testInitializeWithSimpleObject()
    {
        $awareObject = $this->getMockBuilder('stdClass')->getMock();

        $this->container->expects($this->never())->method('get');

        $initializer = $this->initializer;
        $initializer($this->container, $awareObject);
    }
}
