<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Service;

use Interop\Container\ContainerInterface;
use Laminas\Cache\StorageFactory;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for building the cache storage
 *
 * @author Christian Bergau <cbergau86@gmail.com>
 */
class CacheFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return StorageFactory::factory($container->get('BjyAuthorize\Config')['cache_options']);
    }
}
