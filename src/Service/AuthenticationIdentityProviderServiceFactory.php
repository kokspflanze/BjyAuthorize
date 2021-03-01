<?php

/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Simple authentication provider factory
 *
 * @author Ingo Walz <ingo.walz@googlemail.com>
 */
class AuthenticationIdentityProviderServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $user = $container->get('lmcuser_user_service');
        $simpleIdentityProvider = new AuthenticationIdentityProvider($user->getAuthService());
        $config = $container->get('BjyAuthorize\Config');

        $simpleIdentityProvider->setDefaultRole($config['default_role']);
        $simpleIdentityProvider->setAuthenticatedRole($config['authenticated_role']);

        return $simpleIdentityProvider;
    }
}
