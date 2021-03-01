<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Identity\LmcUserLaminasDb;
use Interop\Container\ContainerInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Identity\LmcUserLaminasDb}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class LmcUserLaminasDbIdentityProviderServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $tableGateway \Laminas\Db\TableGateway\TableGateway */
        $tableGateway = new TableGateway('user_role_linker', $container->get('lmcuser_laminas_db_adapter'));
        /* @var $userService \LmcUser\Service\User */
        $userService = $container->get('lmcuser_user_service');
        $config = $container->get('BjyAuthorize\Config');

        $provider = new LmcUserLaminasDb($tableGateway, $userService);

        $provider->setDefaultRole($config['default_role']);

        return $provider;
    }
}
