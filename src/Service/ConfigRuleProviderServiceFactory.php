<?php

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Rule\Config;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Rule\Config}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigRuleProviderServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Config(
            $container->get('BjyAuthorize\Config')['rule_providers'][Config::class]
        );
    }
}
