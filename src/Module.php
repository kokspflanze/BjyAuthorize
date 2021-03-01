<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize;

use BjyAuthorize\Controller\Plugin;
use BjyAuthorize\Guard\AbstractGuard;
use BjyAuthorize\View\Helper;
use BjyAuthorize\View\UnauthorizedStrategy;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerPluginProviderInterface;
use Laminas\ModuleManager\Feature\ViewHelperProviderInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * BjyAuthorize Module
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Module implements
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ControllerPluginProviderInterface,
    ViewHelperProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function onBootstrap(EventInterface $event)
    {
        /* @var $app \Laminas\Mvc\ApplicationInterface */
        $app = $event->getTarget();
        /** @var ServiceManager $serviceManager */
        $serviceManager = $app->getServiceManager();
        $config = $serviceManager->get('BjyAuthorize\Config');
        /** @var UnauthorizedStrategy $strategy */
        $strategy = $serviceManager->get($config['unauthorized_strategy']);
        /** @var AbstractGuard[] $guards */
        $guards = $serviceManager->get('BjyAuthorize\Guards');

        // TODO remove in 3.0.0, fix alias
        if ($serviceManager instanceof ServiceManager && $serviceManager->has('lmcuser_user_service') === false) {
            $serviceManager->setAllowOverride(true);
            $serviceManager->setAlias('lmcuser_user_service', 'zfcuser_user_service');
            $serviceManager->setAllowOverride(false);
        }

        foreach ($guards as $guard) {
            $guard->attach($app->getEventManager());
        }

        $strategy->attach($app->getEventManager());
    }

    /**
     * {@inheritDoc}
     */
    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'isAllowed' => Helper\IsAllowedFactory::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'isAllowed' => Plugin\IsAllowedFactory::class
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
