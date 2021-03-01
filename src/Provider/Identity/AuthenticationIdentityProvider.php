<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Provider\Identity;

use BjyAuthorize\Exception\InvalidRoleException;
use BjyAuthorize\Provider\Role\ProviderInterface as RoleProviderInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * Simple identity provider to handle simply guest|user
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProvider implements ProviderInterface
{
    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var string|\Laminas\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole = 'guest';

    /**
     * @var string|\Laminas\Permissions\Acl\Role\RoleInterface
     */
    protected $authenticatedRole = 'user';

    /**
     * @param AuthenticationService $authService
     */
    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        if (!$identity = $this->authService->getIdentity()) {
            return [$this->defaultRole];
        }

        if ($identity instanceof RoleInterface) {
            return [$identity];
        }

        if ($identity instanceof RoleProviderInterface) {
            return $identity->getRoles();
        }

        return [$this->authenticatedRole];
    }

    /**
     * Get the rule that's used if you're not authenticated
     *
     * @return string|\Laminas\Permissions\Acl\Role\RoleInterface
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

    /**
     * Set the rule that's used if you're not authenticated
     *
     * @param $defaultRole
     *
     * @throws \BjyAuthorize\Exception\InvalidRoleException
     */
    public function setDefaultRole($defaultRole)
    {
        if (!($defaultRole instanceof RoleInterface || is_string($defaultRole))) {
            throw InvalidRoleException::invalidRoleInstance($defaultRole);
        }

        $this->defaultRole = $defaultRole;
    }

    /**
     * Get the role that is used if you're authenticated and the identity provides no role
     *
     * @return string|\Laminas\Permissions\Acl\Role\RoleInterface
     */
    public function getAuthenticatedRole()
    {
        return $this->authenticatedRole;
    }

    /**
     * Set the role that is used if you're authenticated and the identity provides no role
     *
     * @param string|\Laminas\Permissions\Acl\Role\RoleInterface $authenticatedRole
     *
     * @throws \BjyAuthorize\Exception\InvalidRoleException
     *
     */
    public function setAuthenticatedRole($authenticatedRole)
    {
        if (!($authenticatedRole instanceof RoleInterface || is_string($authenticatedRole))) {
            throw InvalidRoleException::invalidRoleInstance($authenticatedRole);
        }

        $this->authenticatedRole = $authenticatedRole;
    }
}
