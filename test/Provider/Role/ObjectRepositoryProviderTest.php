<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Provider\Role;

use BjyAuthorize\Acl\Role;
use BjyAuthorize\Provider\Role\ObjectRepositoryProvider;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

/**
 * {@see \BjyAuthorize\Provider\Role\ObjectRepositoryProvider} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ObjectRepositoryProviderTest extends TestCase
{
    /**
     * @var \BjyAuthorize\Provider\Role\ObjectRepositoryProvider
     */
    private $provider;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repository;

    /**
     * @covers \BjyAuthorize\Provider\Role\ObjectRepositoryProvider::__construct
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(ObjectRepository::class);
        $this->provider = new ObjectRepositoryProvider($this->repository);
    }

    /**
     * @param string $name
     * @param string $parent
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\BjyAuthorize\Acl\HierarchicalRoleInterface
     */
    private function createRoleMock($name, $parent)
    {
        $role = $this->createMock('BjyAuthorize\Acl\HierarchicalRoleInterface');
        $role->expects($this->atLeastOnce())
            ->method('getRoleId')
            ->will($this->returnValue($name));

        $role->expects($this->atLeastOnce())
            ->method('getParent')
            ->will($this->returnValue($parent));

        return $role;
    }

    /**
     * @covers \BjyAuthorize\Provider\Role\ObjectRepositoryProvider::getRoles
     */
    public function testGetRolesWithNoParents()
    {
        // Set up mocks
        $roles = [
            new \stdClass(), // to be skipped
            $this->createRoleMock('role1', null),
            $this->createRoleMock('role2', null)
        ];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($roles));

        // Set up the expected outcome
        $expects = [
            new Role('role1', null),
            new Role('role2', null),
        ];

        $this->assertEquals($expects, $this->provider->getRoles());
    }

    /**
     * @covers \BjyAuthorize\Provider\Role\ObjectRepositoryProvider::getRoles
     */
    public function testGetRolesWithParents()
    {
        // Setup mocks
        $role1 = $this->createRoleMock('role1', null);
        $roles = [
            $role1,
            $this->createRoleMock('role2', null),
            $this->createRoleMock('role3', $role1)
        ];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($roles));

        // Set up the expected outcome
        $expectedRole1 = new Role('role1', null);
        $expects = [
            $expectedRole1,
            new Role('role2', null),
            new Role('role3', $expectedRole1),
        ];

        $this->assertEquals($expects, $this->provider->getRoles());
    }
}
