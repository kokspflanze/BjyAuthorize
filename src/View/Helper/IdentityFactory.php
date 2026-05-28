<?php
declare(strict_types=1);

namespace BjyAuthorize\View\Helper;

use BjyAuthorize\AuthorizationContext;
use Psr\Container\ContainerInterface;

class IdentityFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new Identity(
            $container->get(AuthorizationContext::class)
        );
    }

}