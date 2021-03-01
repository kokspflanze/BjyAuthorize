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

use \PHPUnit\Framework\TestCase;
use BjyAuthorize\Service\ConfigRoleProviderServiceFactory;
use BjyAuthorize\Provider\Role\Config;
use Interop\Container\ContainerInterface;

/**
 * Test for {@see \BjyAuthorize\Service\ConfigRoleProviderServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigRoleProviderServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ConfigRoleProviderServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new ConfigRoleProviderServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $config = [
            'role_providers' => [
                Config::class => [],
            ],
        ];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $guard = $factory($container,ConfigRoleProviderServiceFactory::class);

        $this->assertInstanceOf(Config::class, $guard);
    }
}
