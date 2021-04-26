<?php

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\HierarchicalRoleInterface;
use BjyAuthorize\Acl\Role;
use Doctrine\Persistence\ObjectRepository;
use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * Role provider based on a {@see \Doctrine\Persistence\ObjectRepository}
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ObjectRepositoryProvider implements ProviderInterface
{
    /**
     * @var \Doctrine\Persistence\ObjectRepository
     */
    protected $objectRepository;

    /**
     * @param \Doctrine\Persistence\ObjectRepository $objectRepository
     */
    public function __construct(ObjectRepository $objectRepository)
    {
        $this->objectRepository = $objectRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        $result = $this->objectRepository->findAll();
        $roles = [];

        // Pass One: Build each object
        foreach ($result as $role) {
            if (!$role instanceof RoleInterface) {
                continue;
            }

            $roleId = $role->getRoleId();
            $parent = null;

            if ($role instanceof HierarchicalRoleInterface && $parent = $role->getParent()) {
                $parent = $parent->getRoleId();
            }

            $roles[$roleId] = new Role($roleId, $parent);
        }

        // Pass Two: Re-inject parent objects to preserve hierarchy
        /* @var $roleObj \BjyAuthorize\Acl\Role */
        foreach ($roles as $roleObj) {
            $parentRoleObj = $roleObj->getParent();

            if ($parentRoleObj && $parentRoleObj->getRoleId()) {
                $roleObj->setParent($roles[$parentRoleObj->getRoleId()]);
            }
        }

        return array_values($roles);
    }
}
