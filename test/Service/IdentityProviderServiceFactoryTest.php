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

use BjyAuthorize\Provider\Identity\ProviderInterface;
use BjyAuthorize\Service\IdentityProviderServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\IdentityProviderServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class IdentityProviderServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\IdentityProviderServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new IdentityProviderServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $identityProvider = $this->createMock(ProviderInterface::class);
        $config = ['identity_provider' => 'foo'];

        $container
            ->expects($this->any())
            ->method('get')
            ->with($this->logicalOr('BjyAuthorize\\Config', 'foo'))
            ->will(
                $this->returnCallback(
                    function ($serviceName) use ($identityProvider, $config) {
                        if ('BjyAuthorize\\Config' === $serviceName) {
                            return $config;
                        }

                        return $identityProvider;
                    }
                )
            );

        $this->assertSame($identityProvider, $factory($container, IdentityProviderServiceFactory::class));
    }
}
