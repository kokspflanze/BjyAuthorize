<?php

namespace BjyAuthorizeTest\Provider\Role;

use \PHPUnit\Framework\TestCase;
use BjyAuthorize\Provider\Role\Config;

/**
 * Config resource provider test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Provider\Role\Config::__construct
     * @covers \BjyAuthorize\Provider\Role\Config::loadRole
     * @covers \BjyAuthorize\Provider\Role\Config::getRoles
     */
    public function testConstructor()
    {
        $config = new Config(
            [
                'role1' => [],
                'role2',
                'role3' => [
                    'children' => ['role4'],
                ],
                'role5' => [
                    'children' => [
                        'role6',
                        'role7' => [],
                    ],
                ],
            ]
        );

        $roles = $config->getRoles();

        $this->assertCount(7, $roles);

        /* @var $role \BjyAuthorize\Acl\Role */
        foreach ($roles as $role) {
            $this->assertInstanceOf('BjyAuthorize\Acl\Role', $role);
            $this->assertContains(
                $role->getRoleId(),
                ['role1', 'role2', 'role3', 'role4', 'role5', 'role6', 'role7']
            );

            if ('role4' === $role->getRoleId()) {
                $this->assertNotNull($role->getParent());
                $this->assertSame('role3', $role->getParent()->getRoleId());
            } elseif ('role6' === $role->getRoleId() || 'role7' === $role->getRoleId()) {
                $this->assertNotNull($role->getParent());
                $this->assertSame('role5', $role->getParent()->getRoleId());
            } else {
                $this->assertNull($role->getParent());
            }
        }
    }
}
