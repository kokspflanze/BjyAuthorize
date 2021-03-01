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
