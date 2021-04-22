<?php

namespace BjyAuthorizeTest\Provider\Identity;

use BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider;
use Laminas\Authentication\AuthenticationService;
use PHPUnit\Framework\TestCase;
use Laminas\Permissions\Acl\Role\RoleInterface;
use BjyAuthorize\Provider\Role\ProviderInterface;
use BjyAuthorize\Exception\InvalidRoleException;

/**
 * {@see \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider} test
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProviderTest extends TestCase
{
    /**
     * @var \Laminas\Authentication\AuthenticationService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $authService;

    /**
     * @var \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider
     */
    protected $provider;

    /**
     * {@inheritDoc}
     *
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::__construct
     */
    protected function setUp(): void
    {
        $this->authService = $this->createMock(AuthenticationService::class);
        $this->provider = new AuthenticationIdentityProvider($this->authService);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getIdentityRoles
     */
    public function testAuthenticationIdentityProviderIfAuthenticated()
    {
        $this->authService->expects($this->once())->method('getIdentity')->will($this->returnValue('foo'));

        $this->provider->setDefaultRole('guest');
        $this->provider->setAuthenticatedRole('user');

        $this->assertEquals($this->provider->getIdentityRoles(), ['user']);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getIdentityRoles
     */
    public function testAuthenticationIdentityProviderIfUnauthenticated()
    {
        $this->authService->expects($this->once())->method('getIdentity')->will($this->returnValue(null));

        $this->provider->setDefaultRole('guest');
        $this->provider->setAuthenticatedRole('user');

        $this->assertEquals(['guest'], $this->provider->getIdentityRoles());
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getIdentityRoles
     */
    public function testAuthenticationIdentityProviderIfAuthenticatedWithRoleInterface()
    {
        $this->authService->expects($this->once())->method('getIdentity')->will($this->returnValue('foo'));

        $authorizedRole = $this->getMockBuilder(RoleInterface::class)->getMock();

        $this->provider->setAuthenticatedRole($authorizedRole);

        $this->assertSame([$authorizedRole], $this->provider->getIdentityRoles());
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getIdentityRoles
     */
    public function testAuthenticationIdentityProviderIfUnauthenticatedWithRoleInterface()
    {
        $this->authService->expects($this->once())->method('getIdentity')->will($this->returnValue(null));

        $defaultRole = $this->getMockBuilder(RoleInterface::class)->getMock();

        $this->provider->setDefaultRole($defaultRole);

        $this->assertSame([$defaultRole], $this->provider->getIdentityRoles());
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getIdentityRoles
     */
    public function testGetIdentityRolesRetrievesRolesFromIdentityThatIsARoleProvider()
    {
        $role1 = $this->createMock(RoleInterface::class);
        $role2 = $this->createMock(RoleInterface::class);
        $user = $this->createMock(ProviderInterface::class);

        $user->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue([$role1, $role2]));

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $roles = $this->provider->getIdentityRoles();

        $this->assertCount(2, $roles);
        $this->assertContains($role1, $roles);
        $this->assertContains($role2, $roles);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getIdentityRoles
     */
    public function testGetIdentityRolesRetrievesIdentityThatIsARole()
    {
        $user = $this->createMock(RoleInterface::class);

        $this->authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($user));

        $this->assertSame([$user], $this->provider->getIdentityRoles());
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::setAuthenticatedRole
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getAuthenticatedRole
     * @covers \BjyAuthorize\Exception\InvalidRoleException::invalidRoleInstance
     */
    public function testSetGetAuthenticatedRole()
    {
        $this->provider->setAuthenticatedRole('test');
        $this->assertSame('test', $this->provider->getAuthenticatedRole());

        $role = $this->createMock(RoleInterface::class);
        $this->provider->setAuthenticatedRole($role);
        $this->assertSame($role, $this->provider->getAuthenticatedRole());

        $this->expectException(InvalidRoleException::class);
        $this->provider->setAuthenticatedRole(false);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::setDefaultRole
     * @covers \BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getDefaultRole
     * @covers \BjyAuthorize\Exception\InvalidRoleException::invalidRoleInstance
     */
    public function testSetGetDefaultRole()
    {
        $this->provider->setDefaultRole('test');
        $this->assertSame('test', $this->provider->getDefaultRole());

        $role = $this->createMock(RoleInterface::class);
        $this->provider->setDefaultRole($role);
        $this->assertSame($role, $this->provider->getDefaultRole());

        $this->expectException(InvalidRoleException::class);
        $this->provider->setDefaultRole(false);
    }
}
