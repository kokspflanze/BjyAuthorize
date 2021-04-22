<?php

namespace BjyAuthorize\Service;

use BjyAuthorize\View\UnauthorizedStrategy;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\View\UnauthorizedStrategy}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class UnauthorizedStrategyServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UnauthorizedStrategy($container->get('BjyAuthorize\Config')['template']);
    }
}
