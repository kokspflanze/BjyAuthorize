<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Provider\Role;

use BjyAuthorize\Acl\Role;
use BjyAuthorize\Provider\Role\LaminasDb;
use \PHPUnit\Framework\TestCase;

/**
 * {@see \BjyAuthorize\Provider\Role\LaminasDb} test
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class LaminasDbTest extends TestCase
{
    /**
     * @var \BjyAuthorize\Provider\Role\ObjectRepositoryProvider
     */
    private $provider;

    /**
     * @var \Laminas\ServiceManager\ServiceLocatorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $serviceLocator;

    /**
     * @var \Laminas\Db\TableGateway\TableGateway|\PHPUnit\Framework\MockObject\MockObject
     */
    private $tableGateway;

    /**
     * @covers \BjyAuthorize\Provider\Role\LaminasDb::__construct
     */
    protected function setUp(): void
    {
        $this->serviceLocator = $this->createMock('Laminas\ServiceManager\ServiceLocatorInterface');
        $this->provider       = new LaminasDb([], $this->serviceLocator);
        $this->tableGateway   = $this->getMockBuilder('Laminas\Db\TableGateway\TableGateway')
                                     ->disableOriginalConstructor()
                                     ->getMock();
    }

    /**
     * @covers \BjyAuthorize\Provider\Role\LaminasDb::getRoles
     */
    public function testGetRoles()
    {
        $this->tableGateway->expects($this->any())->method('selectWith')->will(
            $this->returnValue(
                [
                    ['id' => 1, 'role_id' => 'guest', 'is_default' => 1, 'parent_id' => null],
                    ['id' => 2, 'role_id' => 'user', 'is_default' => 0, 'parent_id' => null],
                ]
            )
        );

        $this->serviceLocator->expects($this->any())->method('get')->will($this->returnValue($this->tableGateway));
        $provider = new LaminasDb([], $this->serviceLocator);

        $this->assertEquals($provider->getRoles(), [new Role('guest'), new Role('user')]);
    }

    /**
     * @covers \BjyAuthorize\Provider\Role\LaminasDb::getRoles
     */
    public function testGetRolesWithInheritance()
    {
        $this->tableGateway->expects($this->any())->method('selectWith')->will(
            $this->returnValue(
                [
                    ['id' => 1, 'role_id' => 'guest', 'is_default' => 1, 'parent_id' => null],
                    ['id' => 2, 'role_id' => 'user', 'is_default' => 0, 'parent_id' => 1],
                ]
            )
        );

        $this->serviceLocator->expects($this->any())->method('get')->will($this->returnValue($this->tableGateway));
        $provider = new LaminasDb([], $this->serviceLocator);

        $this->assertEquals($provider->getRoles(), [new Role('guest'), new Role('user', 'guest')]);
    }
}
