<?php

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Provider\Rule\Config;
use BjyAuthorize\Service\ConfigRuleProviderServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\ConfigRuleProviderServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigRuleProviderServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ConfigRuleProviderServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory = new ConfigRuleProviderServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $config = ['rule_providers' => [Config::class => []]];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\\Config')
            ->will($this->returnValue($config));

        $this->assertInstanceOf(Config::class, $factory($container, ConfigRuleProviderServiceFactory::class));
    }
}
