<?php

namespace BjyAuthorizeTest\Service;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\Exception\ContainerException;
use Interop\Container\ContainerInterface;

/**
 * @author Marco Pivetta <ocramius@gmail.com>s
 */
class MockProvider
{
    /**
     * @var array
     */
    public $options;

    /**
     * @var \Interop\Container\ContainerInterface
     */
    public $container;

    /**
     * @param array $options
     * @param \Interop\Container\ContainerInterface $container
     */
    public function __construct(array $options, ContainerInterface $container)
    {
        $this->options = $options;
        $this->container = $container;
    }
}
