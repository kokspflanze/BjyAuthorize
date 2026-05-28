<?php
declare(strict_types=1);

namespace BjyAuthorize\View\Helper;

use BjyAuthorize\AuthorizationContext;
use Laminas\Permissions\Acl\AclInterface;
use Psr\Container\ContainerInterface;

class IsGrantedFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new IsGranted(
            $container->get(AclInterface::class),
            $container->get(AuthorizationContext::class)
        );
    }

}