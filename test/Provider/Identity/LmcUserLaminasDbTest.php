<?php

namespace BjyAuthorizeTest\Provider\Identity;

use BjyAuthorize\Exception\InvalidRoleException;
use BjyAuthorize\Provider\Identity\LmcUserLaminasDb;
use Laminas\Authentication\AuthenticationService;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Permissions\Acl\Role\RoleInterface;
use LmcUser\Service\User;
use PHPUnit\Framework\TestCase;

/**
 * {@see \BjyAuthorize\Provider\Identity\LmcUserLaminasDb} test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class LmcUserLaminasDbTest extends TestCase
{
    /**
     * @var \Laminas\Authentication\AuthenticationService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $authService;

    /**
     * @var \LmcUser\Service\User|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $userService;

    /**
     * @var \Laminas\Db\TableGateway\TableGateway|\PHPUnit\Framework\MockObject\MockObject
     */
    private $tableGateway;

    /**
     * @var \BjyAuthorize\Provider\Identity\LmcUserLaminasDb
     */
    protected $provider;

    /**
     * {@inheritDoc}
     *
     * @covers \BjyAuthorize\Provider\Identity\LmcUserLaminasDb::__construct
     */
    protected function setUp(): void
    {
        $this->authService = $this->createMock(AuthenticationService::class);
        $this->userService = $this->getMockBuilder(User::class)->getMock();
        $this->tableGateway = $this->getMockBuilder(TableGateway::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this
            ->userService
            ->expects($this->any())
            ->method('getAuthService')
            ->will($this->returnValue($this->authService));

        $this->provider = new LmcUserLaminasDb($this->tableGateway, $this->userService);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\LmcUserLaminasDb::getIdentityRoles
     * @covers \BjyAuthorize\Provider\Identity\LmcUserLaminasDb::setDefaultRole
     */
    public function testGetIdentityRolesWithNoAuthIdentity()
    {
        $this->provider->setDefaultRole('test-default');

        $this->assertSame(['test-default'], $this->provider->getIdentityRoles());
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\LmcUserLaminasDb::getIdentityRoles
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

    /**
     * @covers \BjyAuthorize\Provider\Identity\LmcUserLaminasDb::getIdentityRoles
     */
    public function testGetIdentityRoles()
    {
        $roles = $this->provider->getIdentityRoles();
        $this->assertEquals($roles, [null]);
    }
}
