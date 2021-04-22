<?php

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\Role;

/**
 * Array config based Role provider
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Config implements ProviderInterface
{
    /**
     * @var \Laminas\Permissions\Acl\Role\RoleInterface[]
     */
    protected $roles = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $roles = [];

        foreach ($config as $key => $value) {
            if (is_numeric($key)) {
                $roles = array_merge($roles, $this->loadRole($value));
            } else {
                $roles = array_merge($roles, $this->loadRole($key, $value));
            }
        }

        $this->roles = $roles;
    }

    /**
     * @param string $name
     * @param array $options
     * @param string|null $parent
     *
     * @return array
     */
    protected function loadRole($name, $options = [], $parent = null)
    {
        if (isset($options['children']) && count($options['children']) > 0) {
            $children = $options['children'];
        } else {
            $children = [];
        }

        $roles = [];
        $role = new Role($name, $parent);
        $roles[] = $role;

        foreach ($children as $key => $value) {
            if (is_numeric($key)) {
                $roles = array_merge($roles, $this->loadRole($value, [], $role));
            } else {
                $roles = array_merge($roles, $this->loadRole($key, $value, $role));
            }
        }

        return $roles;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
