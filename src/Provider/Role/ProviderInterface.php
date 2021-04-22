<?php

namespace BjyAuthorize\Provider\Role;

/**
 * Role provider interface, provides existing roles list
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @return \Laminas\Permissions\Acl\Role\RoleInterface[]
     */
    public function getRoles();
}
