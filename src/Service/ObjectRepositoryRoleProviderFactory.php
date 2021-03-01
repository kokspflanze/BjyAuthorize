<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use BjyAuthorize\Exception\InvalidArgumentException;
use BjyAuthorize\Provider\Role\ObjectRepositoryProvider;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Role\ObjectRepositoryProvider}
 *
 * @author Tom Oram <tom@scl.co.uk>
 * @author Jérémy Huet <jeremy.huet@gmail.com>
 */
class ObjectRepositoryRoleProviderFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('BjyAuthorize\Config');

        if (!isset($config['role_providers'][ObjectRepositoryProvider::class])) {
            throw new InvalidArgumentException(
                'Config for "BjyAuthorize\Provider\Role\ObjectRepositoryProvider" not set'
            );
        }

        $providerConfig = $config['role_providers'][ObjectRepositoryProvider::class];

        if (!isset($providerConfig['role_entity_class'])) {
            throw new InvalidArgumentException('role_entity_class not set in the bjyauthorize role_providers config.');
        }

        if (!isset($providerConfig['object_manager'])) {
            throw new InvalidArgumentException('object_manager not set in the bjyauthorize role_providers config.');
        }

        /* @var $objectManager \Doctrine\Persistence\ObjectManager */
        $objectManager = $container->get($providerConfig['object_manager']);

        return new ObjectRepositoryProvider($objectManager->getRepository($providerConfig['role_entity_class']));
    }
}
