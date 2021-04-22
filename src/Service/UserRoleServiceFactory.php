<?php

namespace BjyAuthorize\Service;

use Interop\Container\ContainerInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * @author Simone Castellaneta <s.castel@gmail.com>
 *
 * @return \Laminas\Db\TableGateway\TableGateway
 */
class UserRoleServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new TableGateway('user_role', $container->get('bjyauthorize_zend_db_adapter'));
    }
}
