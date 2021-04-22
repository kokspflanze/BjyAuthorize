<?php

namespace BjyAuthorize\Provider\Identity;

/**
 * Interface for identity providers, which are objects capable of
 * retrieving an active identity's role
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
interface ProviderInterface
{
    /**
     * Retrieve roles for the current identity
     *
     * @return string[]|\Laminas\Permissions\Acl\Role\RoleInterface[]
     */
    public function getIdentityRoles();
}
