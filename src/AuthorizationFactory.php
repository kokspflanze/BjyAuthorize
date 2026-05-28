<?php

declare(strict_types=1);

namespace BjyAuthorize;

use Laminas\Permissions\Acl\AclInterface;
use Mezzio\Authorization\AuthorizationInterface;
use Psr\Container\ContainerInterface;

class AuthorizationFactory
{
    public function __invoke(ContainerInterface $container): AuthorizationInterface
    {
        return new Authorization(
            $container->get(AclInterface::class),
            $container->get('config')['bjyauthorize']['ignore_routes'] ?? []
        );
    }


}