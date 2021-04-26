<?php

namespace BjyAuthorize\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Initializer\InitializerInterface;

/**
 * Initializer that injects a {@see \BjyAuthorize\Service\Authorize} in
 * objects that are instances of {@see \BjyAuthorize\Service\AuthorizeAwareInterface}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class AuthorizeAwareServiceInitializer implements InitializerInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Initializer\InitializerInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if (!$instance instanceof AuthorizeAwareInterface) {
            return;
        }

        /* @var $authorize \BjyAuthorize\Service\Authorize */
        $authorize = $container->get(Authorize::class);

        $instance->setAuthorizeService($authorize);
    }
}
