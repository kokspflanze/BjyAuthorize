<?php

namespace BjyAuthorize\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Base factory responsible of instantiating providers
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
abstract class BaseProvidersServiceFactory implements FactoryInterface
{
    const PROVIDER_SETTING = 'providers';

    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('BjyAuthorize\Config');
        $providers = [];

        foreach ($config[static::PROVIDER_SETTING] as $providerName => $providerConfig) {
            if ($container->has($providerName)) {
                $providers[] = $container->get($providerName);
            } else {
                $providers[] = new $providerName($providerConfig, $container);
            }
        }

        return $providers;
    }
}
