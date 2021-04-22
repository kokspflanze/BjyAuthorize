<?php

namespace BjyAuthorize\Acl;

use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * Interface for a role with a possible parent role.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface HierarchicalRoleInterface extends RoleInterface
{
    /**
     * Get the parent role
     *
     * @return \Laminas\Permissions\Acl\Role\RoleInterface|null
     */
    public function getParent();
}
