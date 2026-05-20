<?php

declare(strict_types=1);

namespace BjyAuthorize;

use Mezzio\Authentication\AuthenticationInterface;
use Psr\Container\ContainerInterface;

class AuthenticationFactory
{
    public function __invoke(ContainerInterface $container): AuthenticationMiddleware
    {
        return new AuthenticationMiddleware(
            $container->get(AuthenticationInterface::class),
            $container->get(Authorization::class),
            $container->get(AuthorizationContext::class)
        );
    }

}