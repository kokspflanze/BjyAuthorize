<?php

namespace BjyAuthorize\Service;

use BjyAuthorize\Collector\RoleCollector;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Collector\RoleCollector}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RoleCollectorServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $identityProvider \BjyAuthorize\Provider\Identity\ProviderInterface */
        $identityProvider = $container->get('BjyAuthorize\Provider\Identity\ProviderInterface');

        return new RoleCollector($identityProvider);
    }
}
