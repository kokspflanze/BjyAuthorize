<?php

declare(strict_types=1);

namespace BjyAuthorize;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Permissions\Acl\AclInterface;
use Psr\Container\ContainerInterface;

class AclFactory
{
    public function __invoke(ContainerInterface $container): AclInterface
    {
        $config = $container->get('config')['bjyauthorize'];

        if ($config['role_providers']['type'] !== IdentityRoleInterface::TYPE_DOCTRINE) {
            throw new \UnexpectedValueException('role_providers type mismatch ' . $config['role_providers']['type']);
        }

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get($config['role_providers']['options']['object_manager']);

        $repository = $entityManager->getRepository($config['role_providers']['options']['role_entity_class']);


        $acl = new Acl();
        $roleIdMapping = [];

        /** @var IdentityRoleInterface $role */
        foreach ($repository->findAll() as $role) {
            $roleIdMapping[$role->getId()] = $role->getRoleId();
        }

        /** @var IdentityRoleInterface $role */
        foreach ($repository->findAll() as $role) {
            $acl->addRole($role->getRoleId(), $roleIdMapping[$role->getParentId()] ?? null);
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