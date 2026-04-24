<?php

declare(strict_types=1);

namespace BjyAuthorize;

use Laminas\Permissions\Acl\AclInterface;
use Psr\Container\ContainerInterface;

class AclFactory
{
    public function __invoke(ContainerInterface $container): AclInterface
    {
        $config = $container->get('config')['bjyauthorize'];

        $acl = new Acl();
        $acl->addRole($config['default_role']);

        foreach ($config['role_providers'] as $role) {
            $acl->addRole($role);
        }

        foreach ($config['rule_providers']['allow'] as $rule) {
            if (!$acl->hasResource($rule[1])) {
                $acl->addResource($rule[1]);
            }

            $acl->allow($rule[0], $rule[1]);
        }

        foreach ($config['rule_providers']['deny'] as $rule) {
            if (!$acl->hasResource($rule[1])) {
                $acl->addResource($rule[1]);
            }

            $acl->deny($rule[0], $rule[1]);
        }

        return $acl;
    }
}