<?php

declare(strict_types=1);

namespace BjyAuthorize;

use Laminas\Permissions\Acl\Role\RoleInterface;


class Acl extends \Laminas\Permissions\Acl\Acl
{

    /**
     * @param  RoleInterface|string $role
     * @return array
     */
    public function getRoleParents($role): array
    {
        return $this->getRoleRegistry()->getParents($role);
    }

}