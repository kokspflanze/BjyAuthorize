<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\AuthenticationIdentityProviderServiceFactory;
use Interop\Container\ContainerInterface;
use LmcUser\Service\User;
use PHPUnit\Framework\TestCase;

/**
 * Factory test for {@see \BjyAuthorize\Service\AuthenticationIdentityProviderServiceFactory}
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProviderServiceFactoryTest extends TestCase
{
    /**
     * @covers BjyAuthorize\Service\AuthenticationIdentityProviderServiceFactory::createService
     * @covers BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getDefaultRole
     * @covers BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::getAuthenticatedRole
     */
    public function testCreateService()
    {
        $config = [
            'default_role' => 'test-guest',
            'authenticated_role' => 'test-user',
        ];

        $user = $this->getMockBuilder(User::class)->getMock();
        $auth = $this->createMock('Laminas\\Authentication\\AuthenticationService');
        $container = $this->createMock(ContainerInterface::class);

        $user->expects($this->once())->method('getAuthService')->will($this->returnValue($auth));
        $container
            ->expects($this->any())
            ->method('get')
            ->with($this->logicalOr('lmcuser_user_service', 'BjyAuthorize\\Config'))
            ->will(
                $this->returnCallback(
                    function ($service) use ($user, $config) {
                        if ('lmcuser_user_service' === $service) {
                            return $user;
                        }

                        return $config;
                    }
                )
            );

        $authenticationFactory = new AuthenticationIdentityProviderServiceFactory();
        $authentication = $authenticationFactory($container, AuthenticationIdentityProviderServiceFactory::class);

        $this->assertEquals($authentication->getDefaultRole(), 'test-guest');
        $this->assertEquals($authentication->getAuthenticatedRole(), 'test-user');
    }
}
