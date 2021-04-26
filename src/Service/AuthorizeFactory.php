<?php

namespace BjyAuthorize\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory responsible of building the {@see \BjyAuthorize\Service\Authorize} service
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class AuthorizeFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Authorize($container->get('BjyAuthorize\Config'), $container);
    }
}
